import { IMultiSelectFormConstructorArgs, NumberMultipleSelectForm } from "@/form/generic/single/multi-select-form";
import { ISingleValueForm2Parent } from "@/form/generic/single/single-value-form2";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as ExhibitAJAX from '@/types/ajax/exhibit';
import { route } from "ziggy-js";

export type IConnectedExhibit = Readonly<{
	id: number,
	name: string,
}>;

export interface IConnectedExhibitFromConstructorArgs<R extends boolean = false> extends Pick<IMultiSelectFormConstructorArgs<number, IConnectedExhibit, R>, 'val_ids'|'required'|'on_change'|'selectable_options'> {
}

export class ConnectedExhibitFrom<R extends boolean = false> extends NumberMultipleSelectForm<IConnectedExhibit> {
	public constructor(args: IConnectedExhibitFromConstructorArgs<R>, id: string|number, parent: ISingleValueForm2Parent<IConnectedExhibit[]>) {
		const _args: IMultiSelectFormConstructorArgs<number, IConnectedExhibit, R> = {
			...args,
			search_in: 'name',
			optionLabel: 'name',
		};
		//@ts-expect-error TypeScript-Bug: search_in hat angeblich "nur" den Typ string.
		super(_args, id, parent);
	}
	
	protected async get_shown_suggestions(criteria: string): Promise<Readonly<IConnectedExhibit>[]> {
		if (criteria.length < 4) {
			return [];
		}
		
		const found = await this.ajax_query(criteria);
		const value_in_editing = this.get_value_in_editing();
		if (Array.isArray(value_in_editing)) {
			return found.filter((found_exhibit: IConnectedExhibit): boolean => !value_in_editing.some((exhibit_in_editing: IConnectedExhibit): boolean => exhibit_in_editing.id === found_exhibit.id));
		}
		return [];
	}
	
	private async ajax_query(criteria: string): Promise<Readonly<IConnectedExhibit>[]> {
		const query_params: ExhibitAJAX.ConnectedExhibitsQuery.IQueryParams = {
			criteria: criteria,
		};
		const request_config: AxiosRequestConfig = {
			method: "get",
			url: route('ajax.exhibit.connected.query'),
			params: query_params,
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<ExhibitAJAX.ConnectedExhibitsQuery.I200ResponseData>) => {
				return response.data;
			},
		);
	}
}
