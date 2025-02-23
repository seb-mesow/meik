import * as LocationAJAX from "@/types/ajax/location";
import {
	ILocationFormParent,
	LocationForm,
	UILocationForm
} from "@/form/special/multiple/location-form";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { DataTablePageEvent, DataTableRowEditCancelEvent, DataTableRowEditInitEvent, DataTableRowEditSaveEvent } from "primevue/datatable";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ILocationInitPageProps } from "@/types/page_props/location";
import { route } from "ziggy-js";
import { ref, Ref, shallowRef, ShallowRef } from "vue";

export interface UILocationsForm {
	readonly children: Readonly<ShallowRef<UILocationForm[]>>;
	readonly children_in_editing: Readonly<ShallowRef<UILocationForm[]>>;
	readonly create_button_enabled: Ref<boolean>;
	readonly count_per_page: number;
	readonly total_count: Ref<number>;
	prepend_form(): void;
	on_page(event: DataTablePageEvent): void
	on_row_edit_init(event: DataTableRowEditInitEvent): void;
	on_row_edit_save(event: DataTableRowEditSaveEvent): void;
	on_row_edit_cancel(event: DataTableRowEditCancelEvent): void;
};

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

export class LocationsForm implements UILocationsForm, ILocationFormParent {
	public readonly children: ShallowRef<LocationForm[]>;
	public readonly children_in_editing: ShallowRef<LocationForm[]>;
	public create_button_enabled: Ref<boolean>;
	public count_per_page: number;
	public total_count: Ref<number>;
	
	private toast_service: ToastServiceMethods;
	private confirm_service: ConfirmationServiceMethods;
	
	public constructor(args: ILocationsFormConstructorArgs) {
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		this.children = shallowRef(args.locations.map((_args): LocationForm => new LocationForm({
			data: {
				id: _args.id,
				name: _args.name,
				is_public: _args.is_public,
			},
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		})));
		this.count_per_page = args.count_per_page;
		this.total_count = ref(args.total_count);
		this.create_button_enabled = ref(true);
		this.children_in_editing = shallowRef([]);
	}
	
	public prepend_form(): void {
		const new_child_in_editing: LocationForm = new LocationForm({
			parent: this,
			toast_service: this.toast_service,
			confirm_service: this.confirm_service,
		});
		
		// https://stackoverflow.com/questions/64605833/primevue-editingrows
		// https://stackoverflow.com/questions/68750466/how-do-i-keep-editor-mode-on-when-detecting-invalid-data-with-primevues-datatab
		this.children_in_editing.value = [new_child_in_editing, ...this.children_in_editing.value];
		this.children.value = [new_child_in_editing, ...this.children.value];
	}
	
	public delete_form(location: LocationForm): void {
		console.log(`LocationsForm::delete_form()`);
		this.children.value = this.children.value.filter((rows_location: LocationForm): boolean => rows_location !== location);
	}
	
	public append_form_in_editing(form: LocationForm): void {
		// https://stackoverflow.com/questions/64605833/primevue-editingrows
		// https://stackoverflow.com/questions/68750466/how-do-i-keep-editor-mode-on-when-detecting-invalid-data-with-primevues-datatab
		this.children_in_editing.value = [...this.children_in_editing.value, form];
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
		_data.init_editing();
		// Die zu bearbeitende Zeile wird automatisch zu editing_rows hinzugefügt.
		// Dabei wird nur eine FLACHE Kopie erzeugt.
	}
	
	public async on_row_edit_save(event: DataTableRowEditSaveEvent): Promise<void> {
		console.log('LocationsForm::on_row_edit_save()');
		
		// let { data, newData } = event;
		// data ist das Form
		// newData ist ein davon kopiertes Object
		
		try {
			await this.children.value[event.index].try_save();
			// Die Rows sollen bewusst nicht geupdated werden:
			// Alle vorher angezeigten Zeilen und die neue Zeile sollen zunächst erstmal bleiben.
		} catch(e) {
			console.log('LocationsForm::on_row_edit_save(): EXCEPTION');
			console.log('LocationsForm::on_row_edit_save(): this.children_in_editing.value ===');
			console.log(this.children_in_editing.value);
		}
	};
	
	public async on_row_edit_cancel(event: DataTableRowEditCancelEvent) {
		console.log('LocationsForm::on_row_edit_cancel()');
		// let { data, newData } = event;
		// const _data: LocationForm = data;
		// console.log("_data ==");
		// console.log(_data);
		// console.log("newData ==");
		// console.log(newData);
		// TODo what about newData
		this.children.value[event.index].cancel_editing();
	}
	
	private async ajax_get_paginated(params: { page_number: number, count_per_page: number }): Promise<LocationAJAX.Query.I200ResponseData> {
		console.log(`LocationsForm::ajax.location.get_paginated ${params.page_number} ${params.count_per_page}`);
		
		const request_config: AxiosRequestConfig<never> = {
			method: "get",
			url: route('ajax.location.query'),
			params: params, // bei GET nicht data !
		};
		const response: AxiosResponse<LocationAJAX.Query.I200ResponseData> = await axios.request(request_config);
		return response.data;
	}
	
	private set_children_from_props(props: ILocationInitPageProps[]): void {
		this.children.value = props.map((prop_location: ILocationInitPageProps): LocationForm => {
			return new LocationForm({
				data: {
					id: prop_location.id,
					name: prop_location.name,
					is_public: prop_location.is_public,
				},
				parent: this,
				toast_service: this.toast_service,
				confirm_service: this.confirm_service,
			});
		});
	}
}
