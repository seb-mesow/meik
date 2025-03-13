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
		super(_args, id, parent);
	}
	
	protected async get_shown_suggestions(criteria: string): Promise<Readonly<IConnectedExhibit>[]> {
		return this.ajax_query(criteria);
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
