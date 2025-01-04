export interface ISingleValueForm2<T, IdType = string> {
	readonly id: IdType;
	readonly val: Readonly<T>;
	val_in_editing: T;
	rollback(): void;
	commit(): void;
	errs: readonly string[];
}

export interface ISingleValueForm2ConstructorArgs<T> {
	val: T;
	errs?: string[];
}

export class SingleValueForm2<T, IdType = string> implements ISingleValueForm2<T, IdType> {
	public readonly id: IdType;
	public val: T;
	public val_in_editing: T;
	public errs: string[];
	
	public constructor(args: ISingleValueForm2ConstructorArgs<T>, id: IdType) {
		this.id = id;
		this.val = args.val;
		this.val_in_editing = this.val;
		this.errs = args.errs ?? [];
	}
	
	public rollback(): void {
		this.val_in_editing = this.val;
	}
	
	public commit(): void {
		this.val = this.val_in_editing;
	}
}
