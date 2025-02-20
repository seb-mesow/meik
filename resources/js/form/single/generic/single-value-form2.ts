import { ref, Ref } from "vue";
import { Mutex, MutexInterface } from 'async-mutex';
import { NotUndefined } from "@/types";

export interface UISingleValueForm2<U = string|undefined> {
	readonly html_id: string;
	readonly ui_value_in_editing: Readonly<Ref<U>>;
	readonly ui_is_invalid: Readonly<Ref<boolean>>;
	readonly errs: Readonly<Ref<string[]>>;
	readonly is_required: boolean;
	on_change_ui_value_in_editing(new_ui_value_in_editing: U): void;
}

export interface ISingleValueForm2<T = string> {
	is_valid(): Promise<boolean>;
	commit(): void;
	rollback(): void;
	get_value(): T;
	get_value_in_editing(): T|null|undefined;
	set_value_in_editing(new_value_in_editing: T|null): void;
	set_validate(validate: (value_in_editing: T|null|undefined) => Promise<string[]>): void;
}

export interface ISingleValueForm2Parent<T> {
	register_child(child_form: ISingleValueForm2<T>): void;
	on_child_change(child_form: ISingleValueForm2<T>): void;
}

export interface ValidationError {
	message: string;
}

export interface MultipleValidationErrors {
	errors: ValidationError[];
}

export interface ISingleValueForm2ConstructorArgs<T> {
	val?: T,
	errs?: string[],
	required?: boolean;
	on_change?: (form: ISingleValueForm2<T>) => void,
	validate?: (value_in_editing: T|null|undefined) => Promise<string[]>,
}

/**
 * @param T return value of `get_value()`, MUST NEVER BE `undefined`
 * @param U value of `ui_value_in_editing`
 */
export class SingleValueForm2<T, U = T|undefined> implements
	ISingleValueForm2<T>,
	UISingleValueForm2<U>
{
	private value: T|undefined;
	
	/**
	 * prefilled cache for this.create_value_from_ui_value_and_validate(this.ui_value_in_editing.value);
	 */
	private value_in_editing: T|null|undefined;
	
	public ui_value_in_editing: Ref<U>;
	public errs: Ref<string[]>;
	public ui_is_invalid: Ref<boolean>;
	public readonly html_id: string;
	public readonly is_required: boolean;
	
	private readonly _on_change: (form: ISingleValueForm2<T>) => void;
	private _validate: (value_in_editing: T|null|undefined) => Promise<string[]>;
	private readonly parent: ISingleValueForm2Parent<T>;
	
	private last_validation_state: boolean|undefined = undefined;
	private is_valid_mutex: MutexInterface = new Mutex();
	
	public constructor(args: ISingleValueForm2ConstructorArgs<T>, id: string|number, parent: ISingleValueForm2Parent<T>) {
		this.html_id = typeof id === 'number' ? id.toString() : id;
		this.value = args.val;
		this.value_in_editing = this.value;
		this.is_required = args.required ?? false;
		this.errs = ref(args.errs ?? []);
		//@ts-expect-error
		this.ui_value_in_editing = ref(this.create_ui_value_from_value(this.value));
		this.ui_is_invalid = ref(false);
		this._on_change = args.on_change ?? (() => {});
		this._validate = args.validate ?? (() => Promise.resolve<string[]>([]));
		
		this.parent = parent;
		this.parent.register_child(this);
	}
	
	public on_change_ui_value_in_editing(new_ui_value_in_editing: U): void {
		// TODO awaited hook here for place form
		this.set_value_in_editing_without_ui_value(() => this._create_value_from_ui_value(new_ui_value_in_editing));
	}
	
	public set_value_in_editing(new_value_in_editing: T): void {
		this.ui_value_in_editing.value = this.create_ui_value_from_value(new_value_in_editing);
		this.set_value_in_editing_without_ui_value(() => new_value_in_editing);
	}
	
	public set_validate(validate: (value_in_editing: T|null|undefined) => Promise<string[]>): void {
		this.refresh(() => {
			this._validate = validate;
		});
	}
	
	private set_value_in_editing_without_ui_value(setter: () => T|null|undefined): void {
		this.refresh(() => {
			this.value_in_editing = setter();
		});
	}
	
	public async is_valid(): Promise<boolean> {
		return this.is_valid_mutex.runExclusive(async () => {
			// TODO still return true, if the user had never touched/focused the field
			if (this.last_validation_state !== undefined) {
				return this.last_validation_state;
			} else {
				if (this.is_required && this.value_in_editing === null) {
					this.errs.value.push('Pflichtfeld');
				} else {
					try {
						console.log(`Form ${this.html_id}: start validating ...`)
						const further_errs: string[] = await this._validate(this.value_in_editing);
						console.log(`Form ${this.html_id}: ended validating ...`)
						this.errs.value.push(...further_errs);
					} catch (e) {
						this.handle_exceptions(e);
						// throw errors have priority over returned errors
					}
				}
				const is_valid = this.errs.value.length < 1;
				this.last_validation_state = is_valid;
				return is_valid;
			}
		});
	}
	
	public commit(): void {
		const value = this.get_value_in_editing();
		if (value === undefined) {
			throw new Error(`SingleValueForm2::commit(): ${this.html_id}: value is undefined (=^= something other than 'no value')`);
		}
		if (this.is_required && value === null) {
			throw new Error(`SingleValueForm2::commit(): ${this.html_id}: value is null, but required`);
		}
		this.value = value as T;
	}
	
	public rollback(): void {
		if (this.value === undefined) {
			throw new Error(`SingleValueForm2::rollback(): ${this.html_id}: value is undefined (=^= something other than 'no value')`);
		}
		this.set_value_in_editing(this.value);
	}
	
	public get_value_in_editing(): T|null|undefined {
		return this.value_in_editing;
	}
	
	public get_value(): T {
		if (this.value === undefined) {
			throw new Error(`SingleValueForm2::get_value(): ${this.html_id}: value is undefined (=^= something other than 'no value')`);
		}
		return this.value;
	}
	
	
	/**
	 * receives `null` in the absense of any value
	 * 
	 * never receives `undefined`
	 */
	protected create_ui_value_from_value(value: T): U {
		//@ts-expect-error
		return value;
	}
	
	/**
	 * return `null`, if the user left absolutely nothing
	 * 
	 * return `undefined`, if the user left something, that cannot be converted to a single and valid value
	 */
	protected create_value_from_ui_value(ui_value: U): T|null|undefined {
		//@ts-expect-error
		return ui_value;
	}
	
	private _create_value_from_ui_value(ui_value: U): T|null|undefined {
		try {
			return this.create_value_from_ui_value(ui_value);
		} catch(e) {
			this.handle_exceptions(e);
			return null;
		}
	}
	
	private refresh(inner: () => void): void {
		console.log(`Form ${this.html_id} refreshing`);
		this.errs.value = [];
		
		inner();
		
		this.last_validation_state = undefined;
		// do NOT wait / fire and forget
		async () => {
			this.ui_is_invalid.value = await this.is_valid();
		}
		this._on_change(this);
		this.parent.on_child_change(this);
	}
	
	private handle_exceptions(err: any): void {
		if ('errors' in err && Array.isArray(err.errors)) {
			for (const _err of err.errors) {
				if ('message' in _err && typeof _err.message === 'string') {
					this.errs.value.push(_err.message);
				}
			}
		} else if ('message' in err && typeof err.message === 'string') {
			this.errs.value.push(err.message);
		}
	}
}
