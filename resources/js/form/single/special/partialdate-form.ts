import { ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2 } from "../generic/single-value-form2";
import * as PartialDate from "@/util/partial-date";

export class PartialDateFrom extends SingleValueForm2<PartialDate.PartialDate, string> {
	public constructor(args: ISingleValueForm2ConstructorArgs<PartialDate.PartialDate>, id: string|number, parent: ISingleValueForm2Parent<PartialDate.PartialDate>) {
		args.validate = (value_in_editing) => new Promise<string[]>((resolve) => {
			value_in_editing?.validate();
			resolve([]); // do not forget !
		});
		super(args, id, parent);
	}
	
	protected create_value_from_ui_value(ui_value: string): PartialDate.PartialDate|null {
		return PartialDate.PartialDate.parse_pretty(ui_value);
	}
	
	private handle_errors(e: Error): void {
		if (e instanceof PartialDate.Errors.IError) {
			console.log("single error " + e.message);
			this.errs.value = [e.message];
		} else if (e instanceof PartialDate.Errors.MultipleErrors) {
			console.log("multiple errors");
			this.errs.value = e.errors.map((error) => error.message);
		} else {
			console.log("other error");
			throw e;
		}
	}
	
	protected create_ui_value_from_value(value: PartialDate.PartialDate|null): string {
		return value?.format_pretty() ?? '';
	}
}
