import { ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2 } from "../generic/single-value-form2";
import * as PartialDate from "@/util/partial-date";

export class PartialDateFrom extends SingleValueForm2<PartialDate.PartialDate, string> {
	public constructor(args: ISingleValueForm2ConstructorArgs<PartialDate.PartialDate>, id: string|number, parent: ISingleValueForm2Parent<PartialDate.PartialDate>) {
		args.validate = async (value_in_editing):  Promise<string[]> => {
			value_in_editing?.validate();
			return []; // do not forget !
		};
		super(args, id, parent);
	}
	
	protected create_value_from_ui_value(ui_value: string): PartialDate.PartialDate|null {
		// console.log("PartialDateForm::create_value_from_ui_value(): ui_value ==");
		// console.log(ui_value);
		try {
			return PartialDate.PartialDate.parse_pretty(ui_value);
		} catch (e) {
			throw e;
		}
	}

	protected create_ui_value_from_value(value: PartialDate.PartialDate|null): string {
		return value?.format_pretty() ?? '';
	}
}
