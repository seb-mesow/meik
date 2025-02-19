import { SingleValueForm2 } from "./single-value-form2";

export class StringForm extends SingleValueForm2<string, string|undefined> {
	protected create_value_from_ui_value(ui_value: string|undefined): string|null {
		return (ui_value === '' || ui_value === undefined) ? null : ui_value;
	}
	
	protected create_ui_value_from_value(value: string|null): string|undefined {
		return (value === '' || value === null) ? undefined : value;
	}
}
