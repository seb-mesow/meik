import { ISingleValueForm2, ISingleValueForm2Parent } from "../single/single-value-form2";

export interface IMultipleValueForm {
	is_valid(): Promise<boolean>;
	commit(): void;
	rollback(): void;
	consider(): void;
}

export interface IMultipleValueFormConstructorArgs {
	on_child_change?: (multiple_value_form: IMultipleValueForm) => void;
}

export class MultipleValueForm implements IMultipleValueForm, ISingleValueForm2Parent<any> {
	private readonly children: Map<ISingleValueForm2<any>, boolean|undefined> = new Map();
	private readonly _on_child_change: (multiple_value_form: IMultipleValueForm) => void;
	
	public constructor(args?: IMultipleValueFormConstructorArgs) {
		this._on_child_change = args?.on_child_change ?? (() => Promise.resolve());
	}

	public register_child(child_form: ISingleValueForm2<any>): void {
		this.children.set(child_form, undefined);
	}
	
	// public on_child_validate_end(child_form: ISingleValueForm2<any>): void {
	// 	this.children.set(child_form, child_form.is_valid());
	// 	console.log(`Multiple value form: on_child_validate_end ${child_form.is_valid()}`);
	// 	this.on_update_validation_state(this);
	// }
	public on_child_change(child_form: ISingleValueForm2<any>): void {
		if (!this.children.has(child_form)) {
			throw new Error("Assertation failed: unknown child");
		}
		this.children.set(child_form, undefined);
		
		// do NOT wait / fire and forget
		this._on_child_change(this);
	}
	
	public async is_valid(): Promise<boolean> {
		const undefined_children = this.children.keys().filter((child) => this.children.get(child) === undefined);
		await Promise.all(undefined_children.map(async (child) => {
			this.children.set(child, await child.is_valid());
		}));
		for (const [child, last_validation_state] of this.children) {
			if (last_validation_state === undefined) {
				throw new Error("Assertation failed: some child is still not validated");
			}
			if (last_validation_state === false) {
				return false;
			}
		}
		return true;
	}
	
	public commit(): void {
		this.children.keys().forEach((child) => child.commit());
	}
	
	public rollback(): void {
		this.children.keys().forEach((child) => child.rollback());
	}
	
	public consider(): void {
		this.children.keys().forEach((child) => child.consider());
	}
}
