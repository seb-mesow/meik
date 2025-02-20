import { SingleValueForm2 } from "./single-value-form2";

export class StringForm<R extends boolean = false> extends SingleValueForm2<string, string, R> {
	protected create_value_from_ui_value(ui_value: string): string|null|undefined {
		return (ui_value === '') ? null : ui_value;
	}
	
	protected create_ui_value_from_value(value: string|null): string {
		return (value === null) ? '' : value;
	}
}
