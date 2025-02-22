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
import { ref, Ref, ShallowRef, shallowRef } from "vue";

export interface IPlacesForm {
	readonly children: Readonly<ShallowRef<(IPlaceForm & UIPlaceForm)[]>>;
	readonly children_in_editing: ShallowRef<(IPlaceForm & UIPlaceForm)[]>;
	readonly create_button_enabled: Ref<boolean>;
	readonly count_per_page: number;
	readonly total_count: Ref<number>;
	prepend_new_form(): void;
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
	count_per_page: number, // muss zweckmäßiger im Backend angegeben werden, damit nicht mehr Daten als nötig im Backend geladen werden
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
}

export class PlacesForm implements IPlacesForm, IPlaceFormParent {
	public readonly children: ShallowRef<(IPlaceForm & UIPlaceForm)[]>;
	public readonly children_in_editing: ShallowRef<(IPlaceForm & UIPlaceForm)[]>;
	public readonly create_button_enabled: Ref<boolean>;
	public count_per_page: number;
	public total_count: Ref<number>;
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
		this.count_per_page = args.count_per_page;
		this.total_count = ref(args.total_count);
		this.create_button_enabled = ref(true);
		this.children_in_editing = shallowRef([]);
	}
	
	public prepend_new_form(): void {
		const new_child_in_editing: PlaceForm = new PlaceForm({
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
		console.log(`PlacesForm::delete_form()`);
		this.children.value = this.children.value.filter((rows_place: IPlaceForm): boolean => rows_place !== place);
	}
	
	public append_form_in_editing(form: PlaceForm): void {
		// https://stackoverflow.com/questions/64605833/primevue-editingrows
		// https://stackoverflow.com/questions/68750466/how-do-i-keep-editor-mode-on-when-detecting-invalid-data-with-primevues-datatab
		this.children_in_editing.value = [ ...this.children_in_editing.value, form ];
	}
	
	public async on_page(event: DataTablePageEvent): Promise<void> {
		const data = await this.ajax_get_paginated({ page_number: event.page, count_per_page: event.rows });
		this.set_children_from_props(data.places);
		this.total_count.value = data.total_count;
	}
	
	public async on_row_edit_init(event: DataTableRowEditInitEvent): Promise<void> {
		console.log('PlacesForm::on_row_edit_init()');
		let { data } = event;
		const _data: PlaceForm = data;
		// this.create_button_enabled.value = false;
		_data.init_editing();
		// Die zu bearbeitende Zeile wird automatisch zu editing_rows kopiert.
		// Dabei wird nur eine FLACHE Kopie erzeugt.
	}
	
	public async on_row_edit_save(event: DataTableRowEditSaveEvent): Promise<void> {
		console.log('PlacesForm::on_row_edit_save()');
		// let { data, newData } = event;
		// data ist das Form
		// newData ist ein davon kopiertes Object
		
		try {
			await this.children.value[event.index].try_save();
			// Die Rows sollen bewusst nicht geupdated werden:
			// Alle vorher angezeigten Zeilen und die neue Zeile sollen zunächst erstmal bleiben.
		} catch (e) {}
	};
	
	public async on_row_edit_cancel(event: DataTableRowEditCancelEvent) {
		console.log('PlacesForm::on_row_edit_cancel()');
		// let { data, newData } = event;
		// const _data: PlaceForm = data;
		// console.log("_data ==");
		// console.log(_data);
		// console.log("newData ==");
		// console.log(newData);
		// TODo what about newData
		this.children.value[event.index].cancel_editing();
	}
	
	private async ajax_get_paginated(params: { page_number: number, count_per_page: number }): Promise<PlaceAJAX.Query.I200ResponseData> {
		console.log(`PlacesForm::ajax_get_paginated(): page ${params.page_number}, count_per_page ${params.count_per_page}`);
		
		const _params: PlaceAJAX.Query.IQueryParams = { ...params, location_id: this.location_id };
		const request_config: AxiosRequestConfig<never> = {
			method: "get",
			url: route('ajax.place.query'),
			params: _params // bei GET nicht data !
		};
		const response: AxiosResponse<PlaceAJAX.Query.I200ResponseData> = await axios.request(request_config);
		return response.data;
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
			});
		});
	}
}
