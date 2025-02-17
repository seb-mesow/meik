import { ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";
import * as PartialDate from "@/util/partial-date";

export class PartialDateFrom extends SingleValueForm2<PartialDate.PartialDate, string> {
	public constructor(args: ISingleValueForm2ConstructorArgs<PartialDate.PartialDate>, id: string|number) {
		args.validate = (value_in_editing) => new Promise<string[]>((resolve) => {
			value_in_editing?.validate();
		});
		super(args, id);
	}
	
	protected create_value_from_ui_value(ui_value: string): PartialDate.PartialDate|null {
		// let partial_date: PartialDate.PartialDate|null = null;
		
		try {
			return PartialDate.PartialDate.parse_pretty(ui_value);
		} catch (e) {
			this.handle_errors(e as Error);
			return null;
		}
		
		// try {
		// 	partial_date.validate();
		// } catch(e) {
		// 	this.handle_errors(e as Error);
		// }
		
		// return partial_date;
	}
	
	private handle_errors(e: Error): void {
		if (e instanceof PartialDate.Errors.IError) {
			console.log("single error " + e.message);
			this.errs.value = [e.message];
		} else if (e instanceof PartialDate.Errors.MultipleErrors) {
			console.log("multiple errors");
			this.errs.value = e.errors.map((error) => error.message);
		} else {
			throw e;
		}
	}
	
	protected create_ui_value_from_value(value: PartialDate.PartialDate|null): string {
		return value?.format_pretty() ?? '';
	}
}
