export interface ISingleValueForm2<ValType extends any = string> {
	readonly html_id: string;
	readonly val: Readonly<ValType>|undefined;
	val_in_editing: ValType|undefined;
	rollback(): void;
	commit(): void;
	errs: readonly string[];
	on_change_val_in_editing(val: ValType|undefined): void;
	get_value(): ValType;
}

export interface ISingleValueForm2ConstructorArgs<ValType extends any = string> {
	val?: ValType,
	errs?: string[],
	on_change?: (form: SingleValueForm2<ValType>) => void,
}

export class SingleValueForm2<ValType extends any = string> implements ISingleValueForm2<ValType> {
	public readonly html_id: string;
	public val: ValType|undefined;
	public val_in_editing: ValType|undefined;
	public errs: string[];
	private readonly on_change: (form: SingleValueForm2<ValType>) => void;
	
	public constructor(args: ISingleValueForm2ConstructorArgs<ValType>, id: string|number) {
		this.html_id = typeof id === 'number' ? id.toString() : id;
		this.val = args.val;
		this.val_in_editing = this.val;
		this.errs = args.errs ?? [];
		this.on_change = args.on_change ?? (() => {});
	}
	
	public on_change_val_in_editing(val: ValType): void {
		console.log(`SingleValueForm2: changed == `);
		console.log(`${val}`);
		this.val_in_editing = val;
		this.on_change(this);
	}
	
	public rollback(): void {
		this.val_in_editing = this.val;
	}
	
	public commit(): void {
		this.val = this.val_in_editing;
	}
	
	public get_value(): ValType {
		if (this.val === undefined) {
			throw new Error("SingleValueForm2::get_value(): val is undefined");
		}
		return this.val;
	}
}
