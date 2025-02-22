import * as LocationAJAX from "@/types/ajax/location";
import {
	ILocationForm,
	ILocationFormParent,
	LocationForm
} from "./locationform";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { DataTablePageEvent, DataTableRowEditCancelEvent, DataTableRowEditInitEvent, DataTableRowEditSaveEvent } from "primevue/datatable";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ILocationInitPageProps } from "@/types/page_props/location";
import { route } from "ziggy-js";
import { ref, Ref } from "vue";

export interface ILocationsForm {
	readonly children: Readonly<ILocationForm[]>;
	readonly is_create_button_enabled: boolean;
	readonly children_in_editing: Readonly<ILocationForm[]>;
	readonly count_per_page: number;
	readonly total_count: Ref<number>;
	prepend_form(): void;
	on_page(event: DataTablePageEvent): void
	on_row_edit_init(event: DataTableRowEditInitEvent): void;
	on_row_edit_save(event: DataTableRowEditSaveEvent): void;
	on_row_edit_cancel(event: DataTableRowEditCancelEvent): void;
};

// export type ILocationFormConstructorArgs = Pick<IRealLocationFormConstructorArgs, 
// 	'id' | 'name' | 'is_public'
// >;

export interface ILocationsFormConstructorArgs {
	locations: {
		id: string,
		name: string,
		is_public: boolean,
	}[],
	total_count: number,
	count_per_page: number, // muss zweckmäßiger im Backend angegeben werden, damit nicht mehr Daten als nötig im Backend geladen werden
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
}

export class LocationsForm implements ILocationsForm, ILocationFormParent {
	public children: LocationForm[];
	public readonly children_in_editing: LocationForm[];
	public is_create_button_enabled: boolean;
	public count_per_page: number;
	public total_count: Ref<number>;
	
	private toast_service: ToastServiceMethods;
	private confirm_service: ConfirmationServiceMethods;
	
	public constructor(args: ILocationsFormConstructorArgs) {
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		this.children = args.locations.map((_args): LocationForm => new LocationForm({
			data: {
				id: _args.id,
				name: _args.name,
				is_public: _args.is_public,
			},
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		}));
		this.count_per_page = this.children.length;
		this.total_count = ref(args.total_count);
		this.is_create_button_enabled = true;
		this.children_in_editing = [];
	}
	
	public prepend_form(): void {
		this.is_create_button_enabled = false;
		const new_child_in_editing: LocationForm = new LocationForm({
			delete_button_enabled: false,
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		});
		this.children_in_editing.unshift(new_child_in_editing);
		this.children.unshift(new_child_in_editing);
	}
	
	public delete_form(location: LocationForm): void {
		this.children = this.children.filter((rows_location: LocationForm): boolean => rows_location !== location);
	}
	
	public append_form_in_editing(form: LocationForm): void {
		this.children_in_editing.unshift(form);
	}
	
	public async on_page(event: DataTablePageEvent): Promise<void> {
		const data = await this.ajax_get_paginated({ page_number: event.page, count_per_page: event.rows });
		this.set_children_from_props(data.locations);
		this.total_count.value = data.total_count;
	}
	
	public async on_row_edit_init(event: DataTableRowEditInitEvent): Promise<void> {
		console.log('LocationsForm::on_row_edit_init()');
		let { data } = event;
		const _data: LocationForm = data;
		this.is_create_button_enabled = false;
		_data.init_editing();
		// Die zu bearbeitende Zeile wird automatisch zu editing_rows hinzugefügt.
	}
	
	public async on_row_edit_save(event: DataTableRowEditSaveEvent): Promise<void> {
		console.log('LocationsForm::on_row_edit_save()');
		let { data, newData } = event;
		const _data: LocationForm = data;
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
				this.is_create_button_enabled = true;
			},
			() => {}
		);
	};
	
	public async on_row_edit_cancel(event: DataTableRowEditCancelEvent) {
		console.log('LocationsForm::on_row_edit_cancel()');
		let { data, newData } = event;
		const _data: LocationForm = data;
		console.log("_data ==");
		console.log(_data);
		console.log("newData ==");
		console.log(newData);
		// TODo what about newData
		_data.cancel_editing();
		this.is_create_button_enabled = true;
	}
	
	private async ajax_get_paginated(params: { page_number: number, count_per_page: number }): Promise<LocationAJAX.Query.I200ResponseData> {
		console.log(`ajax.location.get_paginated ${params.page_number} ${params.count_per_page}`);
		const request_config: AxiosRequestConfig<never> = {
			method: "get",
			url: route('ajax.location.query'),
			params: params, // bei GET nicht data !
		};
		const response: AxiosResponse<LocationAJAX.Query.I200ResponseData> = await axios.request(request_config);
		return response.data;
	}
	
	private set_children_from_props(props: ILocationInitPageProps[]): void {
		this.children = props.map((prop_location: ILocationInitPageProps): LocationForm => {
			return new LocationForm({
				data: {
					id: prop_location.id,
					name: prop_location.name,
					is_public: prop_location.is_public,
				},
				parent: this,
				toast_service: this.toast_service,
				confirm_service: this.confirm_service,
				delete_button_enabled: true,
			});
		});
	}
}
