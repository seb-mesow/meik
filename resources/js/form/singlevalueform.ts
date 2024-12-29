export interface ISingleValueForm<T, IdType = string> {
	readonly id: IdType;
	val: T;
	errs: readonly string[];
}

export interface ISingleValueFormConstructorArgs<T> {
	val: T;
	errs?: string[];
}

export class SingleValueForm<T, IdType = string> implements ISingleValueForm<T, IdType> {
	public readonly id: IdType;
	public val: T;
	public errs: string[];
	
	public constructor(args: ISingleValueFormConstructorArgs<T>, id: IdType) {
		this.id = id;
		this.val = args.val;
		this.errs = args.errs ?? [];
	}
}
