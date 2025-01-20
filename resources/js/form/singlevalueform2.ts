export interface ISingleValueForm2<T, IdType = string> {
	readonly id: IdType;
	readonly val: Readonly<T>;
	val_in_editing: T;
	rollback(): void;
	commit(): void;
	errs: readonly string[];
	on_change_val_in_editing(val: T): void;
}

export interface ISingleValueForm2ConstructorArgs<T, IdType = string> {
	val: T,
	errs?: string[],
	on_change?: (form: SingleValueForm2<T, IdType>) => void,
}

export class SingleValueForm2<T, IdType = string> implements ISingleValueForm2<T, IdType> {
	public readonly id: IdType;
	public val: T;
	public val_in_editing: T;
	public errs: string[];
	private readonly on_change: (form: SingleValueForm2<T, IdType>) => void;
	
	public constructor(args: ISingleValueForm2ConstructorArgs<T, IdType>, id: IdType) {
		this.id = id;
		this.val = args.val;
		this.val_in_editing = this.val;
		this.errs = args.errs ?? [];
		this.on_change = args.on_change ?? (() => {});
	}
	
	public on_change_val_in_editing(val: T): void {
		console.log(`SingleValueForm2: changed ${val}`);
		this.val_in_editing = val;
		this.on_change(this);
	}
	
	public rollback(): void {
		this.val_in_editing = this.val;
	}
	
	public commit(): void {
		this.val = this.val_in_editing;
	}
}
