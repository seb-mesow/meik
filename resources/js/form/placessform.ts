import * as PlaceAJAX from "@/types/ajax/place";
import {
	IPlaceForm,
	IPlaceFormParent,
	PlaceForm,
	UIPlaceForm
} from "./placeform";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { DataTablePageEvent, DataTableRowEditCancelEvent, DataTableRowEditInitEvent, DataTableRowEditSaveEvent } from "primevue/datatable";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { IPlaceInitPageProps } from "@/types/page_props/place";
import { route } from "ziggy-js";
import { ShallowRef, shallowRef } from "vue";

export interface IPlacesForm {
	readonly children: Readonly<ShallowRef<(IPlaceForm&UIPlaceForm)[]>>;
	readonly children_in_editing: ShallowRef<PlaceForm[]>;
	readonly create_button_enabled: boolean;
	readonly count_per_page: number;
	readonly total_count: number;
	prepend_form(): void;
	on_page(event: DataTablePageEvent): void
	on_row_edit_init(event: DataTableRowEditInitEvent): void;
	on_row_edit_save(event: DataTableRowEditSaveEvent): void;
	on_row_edit_cancel(event: DataTableRowEditCancelEvent): void;
};

export interface IPlacesFormConstructorArgs {
	location_id: string,
	places: {
		id: string,
		name: string,
	}[],
	total_count: number,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
}

export class PlacesForm implements IPlacesForm, IPlaceFormParent {
	public readonly children: ShallowRef<PlaceForm[]>;
	public readonly children_in_editing: ShallowRef<PlaceForm[]>;
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
		this.children = shallowRef(args.places.map((_args): PlaceForm => new PlaceForm({
			data: _args,
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		})));
		this.count_per_page = this.children.value.length;
		this.total_count = args.total_count;
		this.create_button_enabled = true;
		this.children_in_editing = shallowRef([]);
	}
	
	public prepend_form(): void {
		this.create_button_enabled = false;
		const new_child_in_editing: PlaceForm = new PlaceForm({
			delete_button_enabled: false,
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		});
		
		// https://stackoverflow.com/questions/64605833/primevue-editingrows
		// https://stackoverflow.com/questions/68750466/how-do-i-keep-editor-mode-on-when-detecting-invalid-data-with-primevues-datatab
		this.children_in_editing.value = [ new_child_in_editing, ...this.children_in_editing.value ];
		
		this.children.value.unshift(new_child_in_editing);
	}
	
	public delete_form(place: PlaceForm): void {
		this.children.value = this.children.value.filter((rows_place: PlaceForm): boolean => rows_place !== place);
	}
	
	public append_form_in_editing(form: PlaceForm): void {
		// https://stackoverflow.com/questions/64605833/primevue-editingrows
		// https://stackoverflow.com/questions/68750466/how-do-i-keep-editor-mode-on-when-detecting-invalid-data-with-primevues-datatab
		this.children_in_editing.value = [ ...this.children_in_editing.value, form ];
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
		// Die zu bearbeitende Zeile wird automatisch zu editing_rows kopiert.
		// Dabei wird nur eine FLACHE Kopie erzeugt.
	}
	
	public async on_row_edit_save(event: DataTableRowEditSaveEvent): Promise<void> {
		console.log('PlacesForm::on_row_edit_save()');
		// let { data, newData } = event;
		// data ist das Form
		// newData ist ein davon kopiertes Object
		return this.children.value[event.index].try_save().then(
			() => {
				// Die Rows sollen bewusst nicht geupdated werden:
				// Alle vorher angezeigten Zeilen und die neue Zeile sollen zunächst erstmal bleiben.
				this.create_button_enabled = true;
			},
			() => {},
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
	
	private async ajax_get_paginated(params: PlaceAJAX.Query.IQueryParams): Promise<void> {
		console.log(`ajax.place.get_paginated ${params.page_number} ${params.count_per_page}`);
		const request_config: AxiosRequestConfig<never> = {
			method: "get",
			url: route('ajax.place.get_paginated', { location_id: this.location_id }),
			params: params // bei GET nicht data !
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<PlaceAJAX.Query.I200ResponseData>) => {
				// TODO check
				// @ts-expect-error
				this.count_per_page = params.count_per_page;
				this.set_children_from_props(response.data.places);
				// @ts-expect-error
				this.total_count = response.data.total_count;
			}
		);
	}
	
	private set_children_from_props(props: IPlaceInitPageProps[]): void {
		this.children.value = props.map((prop_place: IPlaceInitPageProps): PlaceForm => {
			return new PlaceForm({
				data: {
					id: prop_place.id,
					name: prop_place.name,
				},
				parent: this,
				toast_service: this.toast_service,
				confirm_service: this.confirm_service,
				delete_button_enabled: true,
			});
		});
	}
}
