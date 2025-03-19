import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { DataTablePageEvent, DataTableRowEditCancelEvent, DataTableRowEditInitEvent, DataTableRowEditSaveEvent } from "primevue/datatable";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { route } from "ziggy-js";
import { ref, Ref, shallowRef, ShallowRef } from "vue";
import { IUserFormParent, IUserRole, UIUserForm, UserForm } from "../multiple/user-form";
import * as UserAJAX from '@/types/ajax/user';
import { IUserInitPageProps } from "@/types/page_props/users";

export interface UIUsersForm {
	readonly children: Readonly<ShallowRef<UIUserForm[]>>;
	readonly children_in_editing: Readonly<ShallowRef<UIUserForm[]>>;
	readonly create_button_enabled: Ref<boolean>;
	readonly count_per_page: number;
	readonly total_count: Ref<number>;
	prepend_form(): void;
	on_page(event: DataTablePageEvent): void
	on_row_edit_init(event: DataTableRowEditInitEvent): void;
	on_row_edit_save(event: DataTableRowEditSaveEvent): void;
	on_row_edit_cancel(event: DataTableRowEditCancelEvent): void;
};

export interface IUserFormConstructorArgs {
	id: string,
	username: string,
	forename: string,
	surname: string,
	role_id: string,
};
export interface IUsersFormSelectableValues {
	role: IUserRole[],
};
export interface IUsersFormConstructorArgs {
	users: IUserFormConstructorArgs[],
	total_count: number,
	count_per_page: number, // muss zweckmäßiger im Backend angegeben werden, damit nicht mehr Daten als nötig im Backend geladen werden
	selectable_values: IUsersFormSelectableValues,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
}

export class UsersForm implements UIUsersForm, IUserFormParent {
	public readonly children: ShallowRef<UserForm[]>;
	public readonly children_in_editing: ShallowRef<UserForm[]>;
	public create_button_enabled: Ref<boolean>;
	public count_per_page: number;
	public total_count: Ref<number>;
	
	private readonly selectables_values: IUsersFormSelectableValues;
	
	private toast_service: ToastServiceMethods;
	private confirm_service: ConfirmationServiceMethods;
	
	public constructor(args: IUsersFormConstructorArgs) {
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		this.selectables_values = args.selectable_values;
		
		this.children = shallowRef(args.users.map((_args: IUserFormConstructorArgs): UserForm => new UserForm({
			data: {
				id: _args.id,
				username: _args.username,
				forename: _args.forename,
				surname: _args.surname,
				role_id: _args.role_id,
			},
			selectable_values: {
				role: args.selectable_values.role,
			},
			parent: this,
			aux: {
				toast_service: this.toast_service,
				confirm_service: this.confirm_service,
			}
		})));
		this.count_per_page = args.count_per_page;
		this.total_count = ref(args.total_count);
		this.create_button_enabled = ref(true);
		this.children_in_editing = shallowRef([]);
	}
	
	public prepend_form(): void {
		const new_child_in_editing: UserForm = new UserForm({
			parent: this,
			selectable_values: {
				role: this.selectables_values.role,
			},
			aux: {
				toast_service: this.toast_service,
				confirm_service: this.confirm_service,
			}
		});
		
		// https://stackoverflow.com/questions/64605833/primevue-editingrows
		// https://stackoverflow.com/questions/68750466/how-do-i-keep-editor-mode-on-when-detecting-invalid-data-with-primevues-datatab
		this.children_in_editing.value = [new_child_in_editing, ...this.children_in_editing.value];
		this.children.value = [new_child_in_editing, ...this.children.value];
	}
	
	public delete_form(user_form: UserForm): void {
		console.log(`UsersForm::delete_form()`);
		this.children.value = this.children.value.filter((_user_form: UserForm): boolean => _user_form !== user_form);
	}
	
	public append_form_in_editing(form: UserForm): void {
		// https://stackoverflow.com/questions/64605833/primevue-editingrows
		// https://stackoverflow.com/questions/68750466/how-do-i-keep-editor-mode-on-when-detecting-invalid-data-with-primevues-datatab
		this.children_in_editing.value = [...this.children_in_editing.value, form];
	}
	
	public async on_page(event: DataTablePageEvent): Promise<void> {
		const data = await this.ajax_get_paginated({ page_number: event.page, count_per_page: event.rows });
		this.set_children_from_props(data.users);
		this.total_count.value = data.total_count;
	}
	
	public async on_row_edit_init(event: DataTableRowEditInitEvent): Promise<void> {
		console.log('UsersForm::on_row_edit_init()');
		let { data } = event;
		const _data: UserForm = data;
		_data.init_editing();
		// Die zu bearbeitende Zeile wird automatisch zu editing_rows hinzugefügt.
		// Dabei wird nur eine FLACHE Kopie erzeugt.
	}
	
	public async on_row_edit_save(event: DataTableRowEditSaveEvent): Promise<void> {
		console.log('UsersForm::on_row_edit_save()');
		
		// let { data, newData } = event;
		// data ist das Form
		// newData ist ein davon kopiertes Object
		
		try {
			await this.children.value[event.index].try_save();
			// Die Rows sollen bewusst nicht geupdated werden:
			// Alle vorher angezeigten Zeilen und die neue Zeile sollen zunächst erstmal bleiben.
		} catch(e) {
			console.log('UsersForm::on_row_edit_save(): EXCEPTION');
			console.log('UsersForm::on_row_edit_save(): this.children_in_editing.value ===');
			console.log(this.children_in_editing.value);
		}
	};
	
	public async on_row_edit_cancel(event: DataTableRowEditCancelEvent) {
		console.log('UsersForm::on_row_edit_cancel()');
		// let { data, newData } = event;
		// const _data: LocationForm = data;
		// console.log("_data ==");
		// console.log(_data);
		// console.log("newData ==");
		// console.log(newData);
		// TODo what about newData
		this.children.value[event.index].cancel_editing();
	}
	
	private async ajax_get_paginated(params: { page_number: number, count_per_page: number }): Promise<UserAJAX.Query.I200ResponseData> {
		console.log(`UsersForm::ajax.user.get_paginated ${params.page_number} ${params.count_per_page}`);
		
		const request_config: AxiosRequestConfig<never> = {
			method: "get",
			url: route('ajax.user.query'),
			params: params, // bei GET nicht data !
		};
		const response: AxiosResponse<UserAJAX.Query.I200ResponseData> = await axios.request(request_config);
		return response.data;
	}
	
	private set_children_from_props(props: IUserInitPageProps[]): void {
		this.children.value = props.map((user_props: IUserInitPageProps): UserForm => {
			return new UserForm({
				data: {
					id: user_props.id,
					username: user_props.username,
					forename: user_props.forename,
					surname: user_props.surname,
					role_id: user_props.role_id,
				},
				selectable_values: this.selectables_values,
				parent: this,
				aux: {
					toast_service: this.toast_service,
					confirm_service: this.confirm_service,
				}
			});
		});
	}
}
