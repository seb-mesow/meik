import { ref, Ref } from "vue";
import { Mutex, MutexInterface } from 'async-mutex';

export interface UISingleValueForm2<U = string|undefined> {
	readonly html_id: string;
	readonly ui_value_in_editing: Readonly<Ref<U>>;
	readonly ui_is_invalid: Readonly<Ref<boolean>>;
	readonly ui_errs: Readonly<Ref<string[]>>;
	readonly is_required: Readonly<Ref<boolean>>;
	on_change_ui_value_in_editing(new_ui_value_in_editing: U): void;
	on_blur(event: Event): void;
}

/**
 * @param T primary return value of `get_value()`, MUST NEVER BE `undefined`
 * @param R whether `get_value()` only returns `T` or `T|null`; default `false`
 */
export interface ISingleValueForm2<T, R extends boolean = false> {
	is_valid(): Promise<boolean>;
	commit(): void;
	rollback(): void;
	get_value(): R extends true ? T : T|null;
	get_value_in_editing(): T|null|undefined;
	set_value_in_editing(new_value_in_editing: T|null): void;
	set_validate(validate: (value_in_editing: T|null|undefined) => Promise<string[]>): void;
	set_is_required(required: boolean): void;
	consider(): void;
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

/**
 * @param T primary return value of `get_value()`, MUST NEVER BE `undefined`
 * @param R whether `get_value()` only returns `T` or `T|null`; default `false`
 */
export interface ISingleValueForm2ConstructorArgs<T, R extends boolean = false> {
	required: R; // There is currently NO WAY to tell TypeScrípt to optionally require a property.
	val: R extends true ? T|undefined : (T|null|undefined);
	errs?: string[];
	on_change?: (form: ISingleValueForm2<T>) => void;
	validate?: (value_in_editing: T|null|undefined) => Promise<string[]>;
};

/**
 * @param T primary return value of `get_value()`, MUST NEVER BE `undefined`
 * @param U value of `ui_value_in_editing`
 * @param R whether `get_value()` only returns `T` or `T|null`; default `false`
 */
export class SingleValueForm2<T, U, R extends boolean = false> implements
	ISingleValueForm2<T, R>,
	UISingleValueForm2<U>
{
	/**
	 * `null` means explicit absense of a value
	 * 
	 * `undefined` means "not yet set and not yet considered"
	 */
	private value: R extends true ? T|undefined : T|undefined|null;
	
	/**
	 * `null` means "No value could be derived from the ui_value"
	 * 
	 * `undefined` means "Something was given, but it can not be converted to an existent and valid value"
	 */
	private value_in_editing: T|null|undefined;
	
	public readonly ui_value_in_editing: Ref<U>;
	public ui_errs: Ref<string[]>;
	public ui_is_invalid: Ref<boolean>;
	public readonly html_id: string;
	public is_required: Ref<R>;
	
	private readonly _on_change: (form: ISingleValueForm2<T>) => void;
	private _validate: (value_in_editing: T|null|undefined) => Promise<string[]>;
	private readonly parent: ISingleValueForm2Parent<T>;
	
	private was_considered: boolean = false;
	private last_validation_state: boolean|undefined = undefined;
	private is_valid_mutex: MutexInterface = new Mutex();
	private errs: string[];
	
	public constructor(args: ISingleValueForm2ConstructorArgs<T, R>, id: string|number, parent: ISingleValueForm2Parent<T>) {
		this.html_id = typeof id === 'number' ? id.toString() : id;
		this.value = args.val;
		this.value_in_editing = this.value;
		
		console.log(`${this.html_id}: init: this.value_in_editing ==`);
		console.log(this.value_in_editing);
		
		//@ts-expect-error
		this.is_required = ref(args.required);
		this.errs = args.errs ?? [];
		this.ui_errs = ref(this.errs);
		
		//@ts-expect-error
		this.ui_value_in_editing = ref(this.create_ui_value_from_value(this.value));
		this.ui_is_invalid = ref(false);
		this._on_change = args.on_change ?? (() => {});
		this._validate = args.validate ?? (() => Promise.resolve<string[]>([]));
		
		this.parent = parent;
		this.parent.register_child(this);
	}
	
	public on_blur(event: Event): void {
		if (!this.was_considered) {
			this.set_value_in_editing_without_ui_value(() => {
				this.was_considered = true;
				let value_in_editing = this.value_in_editing;
				// Before:
				// `undefined` means "no value yet given",
				// `null` means `no value explicitly given`
				// And do not show validation errors yet.
				if (value_in_editing === undefined) {
					value_in_editing = null;
				}
				// After:
				// `undefined` means "no single, valid value could be determianted from the ui_value."
				// `null` means `no value explicitly given
				// And show validation errors from now on.
				return value_in_editing;
			});
		}
	}
	
	public on_change_ui_value_in_editing(new_ui_value_in_editing: U): void {
		this.ui_value_in_editing.value = new_ui_value_in_editing;
		this.set_value_in_editing_without_ui_value(() => {
			this.was_considered = true;
			return this._create_value_from_ui_value(new_ui_value_in_editing);
		});
	}
	
	public set_value_in_editing(new_value_in_editing: T|null|undefined): void {
		this.ui_value_in_editing.value = this.create_ui_value_from_value(new_value_in_editing);
		this.set_value_in_editing_without_ui_value(() => new_value_in_editing);
	}
	
	public set_validate(validate: (value_in_editing: T|null|undefined) => Promise<string[]>): void {
		this.refresh(() => {
			this._validate = validate;
		});
	}
	
	public set_is_required(required: boolean): void {
		this.refresh(() => {
			// @ts-expect-error
			this.is_required.value = required;
		});
	}
	
	public consider(): void {
		this.refresh(() => {
			this.was_considered = true;
		});
	}
	
	private set_value_in_editing_without_ui_value(setter: () => T|null|undefined): void {
		this.refresh(() => {
			this.value_in_editing = setter();
		});
	}
	
	public async is_valid(): Promise<boolean> {
		return this.is_valid_mutex.runExclusive(async () => {

			if (this.last_validation_state !== undefined) {
				return this.last_validation_state;
			} else {
				
				if (this.is_required.value && (this.value_in_editing === null || (!this.was_considered && this.value_in_editing === undefined))) {
					this.errs.push('Pflichtfeld');
				} else if (!(!this.was_considered && !this.is_required.value && this.value_in_editing === undefined)) {
					try {
						console.log(`Form ${this.html_id}: start validating ...`)
						const further_errs: string[] = await this._validate(this.value_in_editing);
						console.log(`Form ${this.html_id}: ended validating ...`)
						this.errs.push(...further_errs);
					} catch (e) {
						this.handle_exceptions(e);
						// throw errors have priority over returned errors
					}
				}
				
				const is_valid = this.errs.length < 1;
				if (this.was_considered) {
					this.ui_is_invalid.value = !is_valid;
					this.ui_errs.value = this.errs;
				} else {
					this.ui_is_invalid.value = false;
					this.ui_errs.value = [];
				}
				
				this.last_validation_state = is_valid;
				return is_valid;
			}
		});
	}
	
	public commit(): void {
		let value = this.get_value_in_editing();
		if (value === undefined) {
			if (this.is_required.value) {
				throw new Error(`SingleValueForm2::commit(): ${this.html_id}: value_in_editing is undefined, but required (=^= something other than 'no value')`);
			} else {
				value = null;
			}
		} else if (value === null && this.is_required.value) {
			throw new Error(`SingleValueForm2::commit(): ${this.html_id}: value_in_editing is null, but required`);
		}
		// @ts-expect-error
		this.value = value;
	}
	
	public rollback(): void {
		this.set_value_in_editing(this.value);
	}
	
	public get_value_in_editing(): T|null|undefined {
		return this.value_in_editing;
	}
	
	public get_value(): R extends true ? T : T|null {
		let value = this.value;
		if (this.value === undefined) {
			if (this.is_required.value) {
				throw new Error(`SingleValueForm2::get_value(): ${this.html_id}: value is undefined, but required (=^= something other than 'no value')`);
			} else {
				// @ts-expect-error
				value = null;
			}
		} else if (this.value === null && this.is_required.value) {
			throw new Error(`SingleValueForm2::get_value(): ${this.html_id}: value is null, but required`);
		}
		// @ts-expect-error
		return value;
	}
	
	
	/**
	 * receives `null` in the explicit absense of any value
	 *
	 * receives `undefined` during initialization of a field in a completely new form or when a not yet considered field is rolled back
	 */
	protected create_ui_value_from_value(value: T|null|undefined): U {
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
		this.errs = [];
		
		inner();
		
		this.last_validation_state = undefined;
		// do NOT wait / fire and forget
		this.is_valid();
		this._on_change(this);
		this.parent.on_child_change(this);
	}
	
	private handle_exceptions(err: any): void {
		if ('errors' in err && Array.isArray(err.errors)) {
			for (const _err of err.errors) {
				if ('message' in _err && typeof _err.message === 'string') {
					this.errs.push(_err.message);
				}
			}
		} else if ('message' in err && typeof err.message === 'string') {
			this.errs.push(err.message);
		}
	}
}
