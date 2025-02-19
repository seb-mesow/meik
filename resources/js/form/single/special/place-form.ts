import { ReactiveFlags } from "vue";
import { ISelectForm, ISelectFormConstructorArgs, SelectForm } from "../generic/select-form";
import { ISingleValueForm2Parent } from "../generic/single-value-form2";

export type IPlace = Readonly<{
	id: string,
	name: string,
}>;

export interface IPlaceForm extends ISelectForm<IPlace> {
	set_selectable_places(selectable_places: IPlace[]): void;
}

export interface IPlaceFormConstructorArgs extends Omit<ISelectFormConstructorArgs<IPlace>, 'get_shown_suggestions'|'validate'> {
	initial_selectable_places?: IPlace[];
}

export class PlaceForm extends SelectForm<IPlace> implements IPlaceForm {
	private selectable_places: IPlace[] = [];
	
	public constructor(args: IPlaceFormConstructorArgs, id: string|number, parent: ISingleValueForm2Parent<IPlace>) {
		const _args: ISelectFormConstructorArgs<IPlace> = {
			...args,
			validate: PlaceForm.get_validate(args.initial_selectable_places ?? []),
		};
		super(_args, id, parent);
		this.selectable_places = args.initial_selectable_places ?? [];
	}
	
	public async set_selectable_places(selectable_places: IPlace[], refresh: boolean = true): Promise<void> {
		this.selectable_places = selectable_places;
		this.set_validate(PlaceForm.get_validate(selectable_places));
	}
	
	private static get_validate(selectable_places: IPlace[]): (value_in_editing: IPlace|null|undefined) => Promise<string[]> {
		return async (value_in_editing) => {
			if (value_in_editing === undefined) {
				return ['Bitte einen auswählbaren Platz angeben'];
			}
			if (value_in_editing === null) {
				return [];
			}
			if (selectable_places.some((place) => place.id === value_in_editing.id)) {
				return [];
			}
			return ['Bitte einen auswählbaren Platz angeben'];
		}
	}
	
	protected create_value_from_ui_value(ui_value: IPlace|string|undefined): IPlace|null|undefined {
		console.log(`PlaceForm::create_value_from_ui_value(): ui_value ==`);
		console.log(ui_value);
		if (!ui_value) {
			return null;
		}
		if (typeof ui_value === 'string') {
			const suggestions = this.search_suggestions(ui_value, (place_name, query) => place_name === query);
			if (suggestions.length === 1) {
				return suggestions[0];
			}
			return undefined; // multiple or no match
		}
		return ui_value;
	}
	
	protected create_ui_value_from_value(value: IPlace|null): IPlace|undefined {
		console.log(`PlaceForm::create_ui_value_from_value(): value ==`);
		console.log(value);
		return value ?? undefined;
	}
	
	protected get_shown_suggestions(query: string): Promise<Readonly<IPlace[]>> {
		return new Promise((resolve) => resolve(this.search_suggestions(query, (place_name, query) => place_name.includes(query))));
	};
	
	private search_suggestions(query: string, filter_func: (place_name: string, query: string) => boolean): IPlace[] {
		query = query.trim().toLowerCase();
		return this.selectable_places.filter((place) => filter_func(place.name.toLowerCase(), query));
	}
}
