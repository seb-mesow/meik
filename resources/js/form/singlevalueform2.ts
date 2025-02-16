import { ref, Ref } from "vue";

export interface UISingleValueForm2<U = string|undefined> {
	readonly html_id: string;
	readonly ui_value_in_editing: Readonly<Ref<U>>;
	readonly errs: Readonly<Ref<string[]>>;
	on_change_val_in_editing(new_ui_value_in_editing: U): void;
}

export interface ISingleValueForm2<T = string> {
	rollback(): void;
	commit(): void;
	get_value(): T;
	get_value_in_editing(): T|null;
}

export interface ISingleValueForm2ConstructorArgs<T = string> {
	val?: T,
	errs?: string[],
	on_change?: (form: ISingleValueForm2<T>) => void,
}

export class SingleValueForm2<T = string, U = T|undefined> implements
	ISingleValueForm2<T>,
	UISingleValueForm2<U>
{
	private value: T|null;
	private value_in_editing: T|null;
	
	public ui_value_in_editing: Ref<U>;
	public errs: Ref<string[]>;
	public readonly html_id: string;
	
	private readonly on_change: (form: ISingleValueForm2<T>) => void;
	
	public constructor(args: ISingleValueForm2ConstructorArgs<T>, id: string|number) {
		this.html_id = typeof id === 'number' ? id.toString() : id;
		this.value = args.val ?? null;
		this.value_in_editing = this.value;
		this.errs = ref(args.errs ?? []);
		//@ts-expect-error
		this.ui_value_in_editing = ref(this.create_ui_value_from_value(this.value));
		this.on_change = args.on_change ?? (() => {});
	}
	
	public on_change_val_in_editing(new_ui_value_in_editing: U): void {
		console.log(`SingleValueForm2: changed == `);
		console.log(new_ui_value_in_editing);
		this.value_in_editing = this._create_value_from_ui_value(new_ui_value_in_editing);
		this.on_change(this);
	}
	
	public rollback(): void {
		this.value_in_editing = this.value;
	}
	
	public commit(): void {
		this.value = this.value_in_editing;
	}
	
	public get_value_in_editing(): T|null {
		return this.value_in_editing;
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
		this.errs.value = [];
		return this.create_value_from_ui_value(ui_value);
	}
}
