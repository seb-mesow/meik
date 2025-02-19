import { GroupSelectForm, IGroupSelectFormConstructorArgs, IGroupType } from "../generic/group-select-form";
import { ISelectFormConstructorArgs, SelectForm } from "../generic/select-form";
import { ISingleValueForm2Parent } from "../generic/single-value-form2";

export type ILocation = Readonly<{
	id: string,
	name: string,
}>;

export interface ILocationFormConstructorArgs extends Omit<ISelectFormConstructorArgs<ILocation>, 'get_shown_suggestions'|'get_option_label'> {
	selectable_locations: ILocation[];
}

export class LocationForm extends SelectForm<ILocation> {
	private readonly selectable_locations: ILocation[];
	
	public constructor(args: ILocationFormConstructorArgs, id: string|number, parent: ISingleValueForm2Parent<ILocation>) {
		super(args, id, parent);
		this.selectable_locations = args.selectable_locations;
	}
	
	protected create_value_from_ui_value(ui_value: string|undefined): ILocation|null|undefined {
		if (!ui_value) {
			return null;
		}
		const suggestions = this.search_suggestions(ui_value, (location_name, query) => location_name === query);
		if (suggestions.length === 1) {
			return suggestions[0];
		}
		return undefined; // multiple or no match
	}
	
	protected create_ui_value_from_value(value: ILocation|null): string|undefined {
		return value ? value.name : undefined;
	}
	
	protected get_shown_suggestions(query: string): Promise<Readonly<ILocation[]>> {
		return new Promise((resolve) => resolve(this.search_suggestions(query, (location_name, query) => location_name.includes(query))));
	};
	
	private search_suggestions(query: string, filter_func: (location_name: string, query: string) => boolean): ILocation[] {
		query = query.trim().toLowerCase();
		return this.selectable_locations.filter((location) => filter_func(location.name.toLowerCase(), query));
	}
}
