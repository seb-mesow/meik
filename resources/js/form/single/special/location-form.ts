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

export interface ILocationFormConstructorArgs extends Pick<ISelectFormConstructorArgs<ILocation>, 'val'|'required'|'on_change'|'validate'|'selectable_options'> {
}

export class LocationForm extends SelectForm<ILocation> implements ILocationForm {
	
	public constructor(args: ILocationFormConstructorArgs, id: string|number, parent: ISingleValueForm2Parent<ILocation>) {
		const _args: ISelectFormConstructorArgs<ILocation> = {
			...args,
			...{
				search_in: 'name',
				optionLabel: 'name',
			},
		};
		super(_args, id, parent);
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
