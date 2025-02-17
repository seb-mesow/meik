import { ref, Ref } from "vue";

export interface UISingleValueForm2<U = string|undefined> {
	readonly html_id: string;
	readonly ui_value_in_editing: Readonly<Ref<U>>;
	readonly errs: Readonly<Ref<string[]>>;
	on_change_ui_value_in_editing(new_ui_value_in_editing: U): void;
}

export interface ISingleValueForm2<T = string> {
	is_valid(): boolean;
	commit(): void;
	rollback(): void;
	get_value(): T;
	set_value_in_editing(new_value_in_editing: T|null): void;
	get_value_in_editing(): T|null;
}

export interface ValidationError {
	message: string;
}

export interface MultipleValidationErrors {
	errors: ValidationError[];
}

export interface ISingleValueForm2ConstructorArgs<T = string> {
	val?: T,
	errs?: string[],
	on_change?: (form: ISingleValueForm2<T>) => void,
	validate?: (value_in_editing: T|null) => Promise<string[]>,
}

export class SingleValueForm2<T = string, U = T|undefined> implements
	ISingleValueForm2<T>,
	UISingleValueForm2<U>
{
	private value: T|null;
	
	/**
	 * prefilled cache for this.create_value_from_ui_value_and_validate(this.ui_value_in_editing.value);
	 */
	private value_in_editing: T|null;
	
	public ui_value_in_editing: Ref<U>;
	public errs: Ref<string[]>;
	public readonly html_id: string;
	
	private readonly on_change: (form: ISingleValueForm2<T>) => void;
	private readonly validate: (value_in_editing: T|null) => Promise<string[]>;
	
	public constructor(args: ISingleValueForm2ConstructorArgs<T>, id: string|number) {
		this.html_id = typeof id === 'number' ? id.toString() : id;
		this.value = args.val ?? null;
		this.value_in_editing = this.value;
		this.errs = ref(args.errs ?? []);
		//@ts-expect-error
		this.ui_value_in_editing = ref(this.create_ui_value_from_value(this.value));
		this.on_change = args.on_change ?? (() => {});
		this.validate = args.validate ?? (() => Promise.resolve([]));
	}
	
	public async on_change_ui_value_in_editing(new_ui_value_in_editing: U): Promise<void> {
		this.errs.value = [];
		this.value_in_editing = this._create_value_from_ui_value(new_ui_value_in_editing);
		await this.validate_value_in_editing();
		
		this.on_change(this);
	}
	
	public is_valid(): boolean {
		return this.errs.value.length < 1;
	}
	
	public commit(): void {
		this.value = this.get_value_in_editing();
	}
	
	public rollback(): void {
		this.value_in_editing = this.value;
		this.ui_value_in_editing.value = this.create_ui_value_from_value(this.value);
	}
	
	public get_value_in_editing(): T|null {
		// if (this.value_in_editing === undefined) {
		// 	this.value_in_editing = this.create_value_from_ui_value_and_validate(this.ui_value_in_editing.value);
		// }
		return this.value_in_editing;
	}
	
	public async set_value_in_editing(new_value_in_editing: T | null): Promise<void> {
		console.log(`new_value_in_editing ==`);
		console.log(new_value_in_editing);
		new Promise<void>(() => {
			this.ui_value_in_editing.value = this.create_ui_value_from_value(new_value_in_editing);
		});
		
		this.errs.value = [];
		this.value_in_editing = new_value_in_editing;
		return this.validate_value_in_editing();
	}
	
	public get_value(): T {
		if (this.value === null) {
			throw new Error("SingleValueForm2::get_value(): val is undefined");
		}
		return this.value;
	}
	
	protected create_ui_value_from_value(value: T|null): U {
		//@ts-expect-error
		return value;
	}
	
	protected create_value_from_ui_value(ui_value: U): T|null {
		//@ts-expect-error
		return ui_value;
	}
	
	private _create_value_from_ui_value(ui_value: U): T|null {
		try {
			return this.create_value_from_ui_value(ui_value);
		} catch(e) {
			this.handle_exceptions(e);
			return null;
		}
	}
	
	private async validate_value_in_editing(): Promise<void> {
		console.log(`prior validation were ${this.errs.value.length} errors`);
		try {
			this.errs.value.push(...(await this.validate(this.value_in_editing)));
		} catch (e) {
			this.handle_exceptions(e);
			// throw errors have priority over returned errors
		}
		console.log(`during validation were ${this.errs.value.length} errors`);
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
