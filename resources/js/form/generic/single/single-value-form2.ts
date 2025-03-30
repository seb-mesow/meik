import { ref, Ref } from "vue";
import { Mutex, MutexInterface } from 'async-mutex';
import deepEqual from "deep-eql";

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
	commit(): void;
	rollback(): void;
	
	get_value(): R extends true ? T : T|null;
	
	get_value_in_editing(): T|null|undefined;
	set_value_in_editing(new_value_in_editing: T|null): void;
	
	consider(): void;
	set_is_required(required: boolean): void;
	set_validate(validate: (value_in_editing: T|null|undefined) => Promise<string[]>): void;
	
	/**
	 * @param preserve Default: `true`
	 */
	add_error(key: string, message: string, preserve?: boolean): void;
	/**
	 * @param only_preserved Default: `true`
	 */
	remove_error(key: string, only_preserved?: boolean): void;
	/**
	 * @param only_preserved Default: `true`
	 */
	clear_errors(only_preserved?: boolean): void;
	
	is_valid(): Promise<boolean>;
}

export interface ISingleValueForm2Parent<T> {
	register_child(child_form: ISingleValueForm2<T>): void;
	on_child_change(child_form: ISingleValueForm2<T>): void;
}

interface Error {
	key: string;
	message: string;
	preserve: boolean;
}

/**
 * @param T primary return value of `get_value()`, MUST NEVER BE `undefined`
 * @param R whether `get_value()` only returns `T` or `T|null`; default `false`
 */
export interface ISingleValueForm2ConstructorArgs<T, R extends boolean = false> {
	required: R; // There is currently NO WAY to tell TypeScrípt to optionally require a property.
	
	/**
	 * initial value
	 */
	val: R extends true ? T|undefined : (T|null|undefined);
	// errs?: string[];
	
	/**
	 * triggered only if the value_in_editing changes
	 * 
	 * Use the "fire-and-forget" idiom to call async functions inside.
	 * Use `await form.is_valid()` to retrieve the latest validation result
	*/
	on_input_change?: (form: ISingleValueForm2<T>) => void;
	
	/**
	 * triggered on EVERY state change: if the ui value changes, validate function changes, required changes, considered, ...
	 * 
	 * Use the "fire-and-forget" idiom to call async functions inside.
	 * Use `await form.is_valid()` to retrieve the latest validation result
	 */
	on_change?: (form: ISingleValueForm2<T>) => void;
	
	/**
	 * triggered on EVERY state change: if the ui value changes, validate function changes, required changes, considered, ...
	 */
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
	private readonly _on_input_change: (form: ISingleValueForm2<T>) => void;
	private _validate: (value_in_editing: T|null|undefined) => Promise<string[]>;
	private readonly parent: ISingleValueForm2Parent<T>;
	
	private was_considered: boolean = false;
	private last_validation_state: boolean|undefined = undefined;
	private is_valid_mutex: MutexInterface = new Mutex();
	private errs: Error[];
	
	public constructor(args: ISingleValueForm2ConstructorArgs<T, R>, id: string|number, parent: ISingleValueForm2Parent<T>) {
		this.html_id = typeof id === 'number' ? id.toString() : id;
		this.value = args.val;
		this.value_in_editing = this.value;
		
		// console.log(`${this.html_id}: init: this.value_in_editing ==`);
		// console.log(this.value_in_editing);
		
		//@ts-expect-error
		this.is_required = ref(args.required);
		this.errs = [];
		this.ui_errs = ref(this.create_ui_errs());
		
		//@ts-expect-error
		this.ui_value_in_editing = ref(this.create_ui_value_from_value(this.value));
		this.ui_is_invalid = ref(false);
		this._on_change = args.on_change ?? (() => {});
		this._on_input_change = args.on_input_change ?? (() => {});
		this._validate = args.validate ?? (() => Promise.resolve<string[]>([]));
		
		this.parent = parent;
		this.parent.register_child(this);
	}
	
	public on_blur(event: Event): void {
		this.consider();
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
		}, 'set_validate');
	}
	
	public set_is_required(required: boolean): void {
		this.refresh(() => {
			// @ts-expect-error
			this.is_required.value = required;
		}, 'set_is_required');
	}
	
	public consider(): void {
		console.log(`SingleValueForm2::consider(): ${this.html_id}`);
		if (!this.was_considered) {
			this.set_value_in_editing_without_ui_value(() => {
				this.was_considered = true;
				let value_in_editing = this.value_in_editing;
				// Before:
				// `undefined` means "no value yet given",
				// `null` means "no value explicitly given"
				// And do not show validation errors yet.
				if (value_in_editing === undefined) {
					value_in_editing = null;
				}
				// After:
				// `undefined` means "no single, valid value could be determianted from the ui_value."
				// `null` means "no value explicitly given"
				// And show validation errors from now on.
				return value_in_editing;
			});
		}
	}
	
	private set_value_in_editing_without_ui_value(setter: () => T|null|undefined): void {
		this.refresh(() => {
			this.value_in_editing = setter();
		}, 'set_value_in_editing_without_ui_value');
	}
	
	public async is_valid(): Promise<boolean> {
		return this.is_valid_mutex.runExclusive(async () => {

			if (this.last_validation_state !== undefined) {
				return this.last_validation_state;
			} else {
				
				// console.log(`Form ${this.html_id}: is_required === ${this.is_required.value}`);
				// console.log(`Form ${this.html_id}: was_considered === ${this.was_considered}`);
				// console.log(`Form ${this.html_id}: value_in_editing ===`);
				// console.log(this.value_in_editing);
				
				if (this.is_required.value && (this.value_in_editing === null || (!this.was_considered && this.value_in_editing === undefined))) {
					this.errs.push({ key: 'required', message: 'Pflichtfeld', preserve: false});
				} else if (!(!this.was_considered && this.value_in_editing === undefined && !this.is_required.value)) {
					try {
						console.log(`Form ${this.html_id}: start validating ...`)
						const further_errs: string[] = await this._validate(this.value_in_editing);
						console.log(`Form ${this.html_id}: ended validating ...`)
						this.errs = this.errs.concat(further_errs.map((str: string): Error => {
							return { key: '__NOT_PRESERVE__' + str, message: str, preserve: false };
						}));
					} catch (e) {
						this.handle_exceptions(e);
						// throw errors have priority over returned errors
					}
				}
				
				const is_valid = this.errs.length < 1;
				if (this.was_considered) {
					this.ui_is_invalid.value = !is_valid;
					this.ui_errs.value = this.create_ui_errs();
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
	
	private refresh(inner: () => void, cause: string, only_if_errs_changed: boolean = false): void {
		const errs_backup = [...this.errs];
		const value_in_editing_backup = structuredClone(this.value_in_editing);
		
		// only clear not preseved errors
		this.errs = this.errs.filter((err: Error): boolean => err.preserve);
		
		inner();
				
		if (only_if_errs_changed) {
			if (this.errs.length === errs_backup.length) {
				let difference = false;
				for (let i = 0; i < errs_backup.length; i++) {
					const e1 = this.errs[i];
					const e2 = errs_backup[i];
					if (e1.key !== e1.key || e1.message !== e2.message || e1.preserve !== e2.preserve) {
						difference = true;
						break;
					}
				}
				if (!difference) {
					return;
				}
			}
		}
		
		this.last_validation_state = undefined;
		// do NOT wait / fire and forget
		this.is_valid(); // would regenerate not preserved errors
		this._on_change(this);
		if (!deepEqual(this.value_in_editing, value_in_editing_backup)) {
			this._on_input_change(this);
		}
		this.parent.on_child_change(this);
	}
	
	private handle_exceptions(err: any): void {
		if ('errors' in err && Array.isArray(err.errors)) {
			for (const _err of err.errors) {
				if ('message' in _err && typeof _err.message === 'string') {
					this.errs.push({ key: _err.message, message: _err.message, preserve: false});
				}
			}
		} else if ('message' in err && typeof err.message === 'string') {
			this.errs.push({ key: err.message, message: err.message, preserve: false});
		}
	}
	
	private create_ui_errs(): string[] {
		return this.errs.map((err: Error): string => err.message);
	}
	
	/**
	 * should not be used inside the `on_change` event callback
	 */
	public add_error(key: string, message: string, preserve: boolean = true): void {
		const index = this.errs.findIndex((err: Error): boolean => err.key === key);
		if (index >= 0) {
			this.errs[index].message = message;
			return;
		}
		
		this.refresh(() => {
			// Not preserved errors enter by this function will count into the validation result.
			this.errs.push({
				key: key,
				message: message,
				preserve: preserve,
			});
		}, 'add_error', true);
	}
	
	/**
	 * should not be used inside the `on_change` event callback
	 */
	public remove_error(key: string, only_preserved: boolean = true): void {
		this.refresh(() => {
			this.errs = this.errs.filter((err: Error): boolean => {
				if (err.key === key) {
					if (only_preserved) {
						return !err.preserve;
					}
					return false;
				}
				return true;
			});
		}, 'remove_error', true);
	}
	
	/**
	 * should not be used inside the `on_change` event callback
	 */
	public clear_errors(only_preserved: boolean = true): void {
		this.refresh(() => {
			if (only_preserved) {
				this.errs = this.errs.filter((err: Error): boolean => !err.preserve);
			} else {
				this.errs = [];
			}
		}, 'clear_errors', true);
	}
}
