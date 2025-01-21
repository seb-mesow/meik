import {
	IGetPlacesPaginated200ResponseData,
	IGetPlacesPaginatedQueryParams
} from "@/types/ajax/place";
import {
	IPlaceForm,
	IPlaceFormParent,
	IPlaceFormConstructorArgs as IRealPlaceFormConstructorArgs,
	PlaceForm
} from "./placeform";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { DataTablePageEvent, DataTableRowEditCancelEvent, DataTableRowEditInitEvent, DataTableRowEditSaveEvent } from "primevue/datatable";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { IPlaceInitPageProps } from "@/types/page_props/place";
import { route } from "ziggy-js";

export interface IPlacesForm {
	readonly children: Readonly<IPlaceForm[]>;
	readonly create_button_enabled: boolean;
	readonly children_in_editing: Readonly<IPlaceForm[]>;
	readonly count_per_page: number;
	readonly total_count: number;
	prepend_form(): void;
	on_page(event: DataTablePageEvent): void
	on_row_edit_init(event: DataTableRowEditInitEvent): void;
	on_row_edit_save(event: DataTableRowEditSaveEvent): void;
	on_row_edit_cancel(event: DataTableRowEditCancelEvent): void;
};

export type IPlaceFormConstructorArgs = Pick<IRealPlaceFormConstructorArgs, 
	'id' | 'name'
>;

export interface IPlacesFormConstructorArgs {
	location_id: string,
	places: IPlaceFormConstructorArgs[],
	total_count: number,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
}

export class PlacesForm implements IPlacesForm, IPlaceFormParent {
	public children: PlaceForm[];
	public readonly children_in_editing: PlaceForm[];
	public create_button_enabled: boolean;
	public count_per_page: number;
	public total_count: number;
	public readonly location_id: string;
	
	private toast_service: ToastServiceMethods;
	private confirm_service: ConfirmationServiceMethods;
	
	public constructor(args: IPlacesFormConstructorArgs) {
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		this.location_id = args.location_id;
		this.children = args.places.map((_args: IPlaceFormConstructorArgs): PlaceForm => new PlaceForm({
			id: _args.id,
			name: _args.name,
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		}));
		this.count_per_page = this.children.length;
		this.total_count = args.total_count;
		this.create_button_enabled = true;
		this.children_in_editing = [];
	}
	
	public prepend_form(): void {
		this.create_button_enabled = false;
		const new_child_in_editing: PlaceForm = new PlaceForm({
			delete_button_enabled: false,
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		});
		this.children_in_editing.unshift(new_child_in_editing);
		this.children.unshift(new_child_in_editing);
	}
	
	public delete_form(place: PlaceForm): void {
		this.children = this.children.filter((rows_place: PlaceForm): boolean => rows_place !== place);
	}
	
	public append_form_in_editing(form: PlaceForm): void {
		this.children_in_editing.unshift(form);
	}
	
	public async on_page(event: DataTablePageEvent): Promise<void> {
		return this.ajax_get_paginated({ page_number: event.page, count_per_page: event.rows });
	}
	
	public async on_row_edit_init(event: DataTableRowEditInitEvent): Promise<void> {
		console.log('PlacesForm::on_row_edit_init()');
		let { data } = event;
		const _data: PlaceForm = data;
		this.create_button_enabled = false;
		_data.init_editing();
		// Die zu bearbeitende Zeile wird automatisch zu editing_rows hinzugefügt.
	}
	
	public async on_row_edit_save(event: DataTableRowEditSaveEvent): Promise<void> {
		console.log('PlacesForm::on_row_edit_save()');
		let { data, newData } = event;
		const _data: PlaceForm = data;
		// data ist das Form
		// newData ist ein davon kopiertes Object
		console.log("_data ==");
		console.log(_data);
		console.log("newData ==");
		console.log(newData);
		_data.try_save(newData).then(
			() => {
				// Die Rows sollen bewusst nicht geupdated werden:
				// Alle vorher angezeigten Zeilen und die neue Zeile sollen zunächst erstmal bleiben.
				this.create_button_enabled = true;
			},
			() => {}
		);
	};
	
	public async on_row_edit_cancel(event: DataTableRowEditCancelEvent) {
		console.log('PlacesForm::on_row_edit_cancel()');
		let { data, newData } = event;
		const _data: PlaceForm = data;
		console.log("_data ==");
		console.log(_data);
		console.log("newData ==");
		console.log(newData);
		// TODo what about newData
		_data.cancel_editing();
		this.create_button_enabled = true;
	}
	
	private async ajax_get_paginated(params: IGetPlacesPaginatedQueryParams): Promise<void> {
		console.log(`ajax.place.get_paginated ${params.page_number} ${params.count_per_page}`);
		const request_config: AxiosRequestConfig<IGetPlacesPaginatedQueryParams> = {
			method: "get",
			url: route('ajax.place.get_paginated', { location_id: this.location_id }),
			params: params // bei GET nicht data !
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<IGetPlacesPaginated200ResponseData>) => {
				this.count_per_page = params.count_per_page;
				this.set_children_from_props(response.data.places);
				this.total_count = response.data.total_count;
			}
		);
	}
	
	private set_children_from_props(props: IPlaceInitPageProps[]): void {
		this.children = props.map((prop_place: IPlaceInitPageProps): PlaceForm => {
			return new PlaceForm({
				id: prop_place.id,
				name: { 
					val: prop_place.name,
					errs: [],
				},
				parent: this,
				toast_service: this.toast_service,
				confirm_service: this.confirm_service,
				delete_button_enabled: true,
			});
		});
	}
}
