import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { ISelectForm, ISelectFormConstructorArgs, SelectForm } from "../generic/select-form";
import { ISingleValueForm2Parent } from "../generic/single-value-form2";
import { IPlace } from "./place-form";
import * as PlaceAJAX from '@/types/ajax/place';
import { route } from "ziggy-js";

export type ILocation = Readonly<{
	id: string,
	name: string,
}>;

export interface ILocationForm extends ISelectForm<ILocation> {
	get_all_places_in_value_in_editing(): Promise<IPlace[]>;
};

export interface ILocationFormConstructorArgs extends Omit<ISelectFormConstructorArgs<ILocation>, 'get_shown_suggestions'> {
	selectable_locations: ILocation[];
}

export class LocationForm extends SelectForm<ILocation> implements ILocationForm {
	private readonly selectable_locations: ILocation[];
	
	public constructor(args: ILocationFormConstructorArgs, id: string|number, parent: ISingleValueForm2Parent<ILocation>) {
		super(args, id, parent);
		this.selectable_locations = args.selectable_locations;
	}
	
	protected create_value_from_ui_value(ui_value: ILocation|string|undefined): ILocation|null|undefined {
		console.log(`LocationForm::create_value_from_ui_value(): ui_value ==`);
		console.log(ui_value);
		if (!ui_value) {
			return null;
		}
		if (typeof ui_value === 'string') {
			const suggestions = this.search_suggestions(ui_value, (location_name, query) => location_name === query);
			if (suggestions.length === 1) {
				return suggestions[0];
			}
			return undefined; // multiple or no match
		}
		return ui_value;
	}
	
	protected create_ui_value_from_value(value: ILocation|null): ILocation|undefined {
		console.log(`LocationForm::create_ui_value_from_value(): value ==`);
		console.log(value);	
		return value ?? undefined;
	}
	
	protected get_shown_suggestions(query: string): Promise<Readonly<ILocation[]>> {
		return new Promise((resolve) => resolve(this.search_suggestions(query, (location_name, query) => location_name.includes(query))));
	};
	
	private search_suggestions(query: string, filter_func: (location_name: string, query: string) => boolean): ILocation[] {
		query = query.trim().toLowerCase();
		return this.selectable_locations.filter((location) => filter_func(location.name.toLowerCase(), query));
	}
	
	public async get_all_places_in_value_in_editing(): Promise<IPlace[]> {
		const value_in_editing  = this.get_value_in_editing();
		if (!value_in_editing) {
			throw new Error("undefined value_in_editing");
		}
		
		const query_params: PlaceAJAX.Query.IQueryParams = {
			location_id: value_in_editing.id,
		};
		
		const request_config: AxiosRequestConfig<never> = {
			method: "get",
			url: route('ajax.place.query'),
			params: query_params,
		};
		
		return axios.request(request_config).then(
			(response: AxiosResponse<PlaceAJAX.Query.I200ResponseData>) => {
				return response.data.places;
			},
		);
	}
}
