import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { ISelectForm, ISelectFormConstructorArgs, SelectForm } from "../../generic/single/select-form";
import { ISingleValueForm2Parent } from "../../generic/single/single-value-form2";
import { IPlace } from "@/form/special/single/place-form";
import * as PlaceAJAX from '@/types/ajax/place';
import { route } from "ziggy-js";

export type ILocation = Readonly<{
	id: string,
	name: string,
}>;

export interface ILocationForm<R extends boolean = false> extends ISelectForm<ILocation, R> {
	get_all_places_in_value_in_editing(): Promise<IPlace[]>;
};

export interface ILocationFormConstructorArgs<R extends boolean = false> extends Pick<ISelectFormConstructorArgs<ILocation, R>, 'val_id'|'required'|'on_change'|'validate'|'selectable_options'> {
}

export class LocationForm<R extends boolean = false> extends SelectForm<ILocation, R> implements ILocationForm<R> {
	public constructor(args: ILocationFormConstructorArgs<R>, id: string|number, parent: ISingleValueForm2Parent<ILocation>) {
		const _args: ISelectFormConstructorArgs<ILocation, R> = {
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
