import { ISingleValueForm2, UISingleValueForm2, SingleValueForm2, ISingleValueForm2Parent } from "../../generic/single/single-value-form2";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { route } from "ziggy-js";
import * as UserAJAX from '@/types/ajax/user';
import { ToastServiceMethods } from "primevue/toastservice";
import { ref, Ref } from "vue";
import { StringForm } from "../../generic/single/string-form";
import { IMultipleValueForm, MultipleValueForm } from "../../generic/multiple/multiple-value-form";
import { ISimpleSelectForm, StringSimpleSelectForm, UISimpleSelectForm } from "@/form/generic/single/simple-select-form";

export interface UINewUserForm {
	readonly forename: Readonly<UISingleValueForm2<string>>;
	readonly surname: Readonly<UISingleValueForm2<string>>;
	readonly role: Readonly<UISimpleSelectForm<IUserRole>>;
	readonly username: Readonly<UISingleValueForm2<string>>;
	readonly password: Readonly<UISingleValueForm2<string>>;
	readonly password_again: Readonly<UISingleValueForm2<string>>;
	
	readonly ui_errs: Readonly<Ref<string[]>>;
	
	click_save(): void;
	
	readonly is_save_button_enabled: Readonly<Ref<boolean>>;
	readonly is_save_button_loading: Readonly<Ref<boolean>>;
}

export type IUserRole = Readonly<{
	id: string,
	name: string,
}>;

export interface INewUserConstructorArgs {
	// data?: {
	// },
	
	// // Voreinstellungen
	// preset?: {
	// 	remember: boolean,
	// },
	
	// Auswahlwerte
	selectable_values: {
		role: IUserRole[],
	},
	
	// Hilfsobjekte
	aux: {
		toast_service: ToastServiceMethods,
	}
}

export class NewUserForm implements UINewUserForm {
	// Kerndaten
	public readonly forename: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly surname: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly username: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly password: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly password_again: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly role: ISimpleSelectForm<string, IUserRole, true> & UISimpleSelectForm<IUserRole>;
	
	// Hilfspbjekte
	private readonly toast_service: ToastServiceMethods;
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any>;
	
	// UI-Werte
	public readonly ui_errs: Ref<string[], string[]>;
	public readonly is_save_button_enabled: Ref<boolean> = ref(false);
	public readonly is_save_button_loading: Ref<boolean> = ref(false);
	
	public constructor(args: INewUserConstructorArgs) {
		// Hilfsobjekte
		this.toast_service = args.aux.toast_service;
		
		this.fields = new MultipleValueForm({
			on_child_change: async (form) => {
				console.log(`NewUserForm: fields: is_valid() == ${await form.is_valid()}`);
				this.is_save_button_enabled.value = await form.is_valid();
			},
		});
		
		// UI-Werte
		this.ui_errs = ref([]);
		
		// Kerndaten
		this.forename = new StringForm<true>({
			val: undefined,
			required: true,
			on_change: async (form) => {
				console.log(`NewUserForm: forename: is_valid() == ${await form.is_valid()}`)
			}
		}, 'username', this.fields);
		
		this.surname = new StringForm<true>({
			val: undefined,
			required: true,
			on_change: async (form) => {
				console.log(`NewUserForm: surname: is_valid() == ${await form.is_valid()}`)
			}
		}, 'username', this.fields);
		
		this.username = new StringForm<true>({
			val: undefined,
			required: true,
			on_change: async (form) => {
				console.log(`NewUserForm: username: is_valid() == ${await form.is_valid()}`)
			}
		}, 'username', this.fields);
		
		this.password = new StringForm<true>({
			val: undefined,
			required: true,
			on_change: async (form) => {
				// Wenn du wissen willst was Rekursion ist, dann schaue bei this.password_again.on_change() .
				if (form.get_value_in_editing() !== this.password_again.get_value_in_editing()) {
					return this.password_again.add_error('mismatch', 'Die Passwörter stimmen nicht überein.');
				}
				return this.password_again.remove_error('mismatch');
			}
		}, 'password', this.fields);
		
		this.password_again = new StringForm<true>({
			val: undefined,
			required: true,
			on_change: async (form) => {
				// Wenn du wissen willst was Rekursion ist, dann schaue bei this.password.on_change() .
				if (form.get_value_in_editing() !== this.password.get_value_in_editing()) {
					return this.password_again.add_error('mismatch', 'Die Passwörter stimmen nicht überein.');
				}
				return this.password_again.remove_error('mismatch');
			}
		}, 'password_again', this.fields);
		
		this.role = new StringSimpleSelectForm<IUserRole, true>({
			val_id: undefined,
			selectable_options: args.selectable_values.role,
			required: true,
			validate: async (value_in_editing) => {
				if (value_in_editing == undefined) {
					return ['Bitte eine auswählbare Benutzerrolle angeben'];
				}
				return [];
			},
		}, 'role', this.fields);
	}
	
	private success_toast(msg: string): void {
		this.toast_service.add({ severity: 'success', summary: msg, life: 3000 });
	}
	
	private failed_toast(msg: string): void {
		this.toast_service.add({ severity: 'error', summary: msg, life: 3000 });
	}
	
	public async click_save(): Promise<void> {
		// this.ui_errs.value = [];
		this.fields.commit();
		
		this.is_save_button_loading.value = true;
		await this.ajax_create();
		this.is_save_button_loading.value = false;
	}
	
	private async ajax_create(): Promise<void> {
		const request_data: UserAJAX.Create.IRequestData = {
			forename: this.forename.get_value(),
			surname: this.surname.get_value(),
			username: this.username.get_value(),
			password: this.password.get_value(),
			role_id: this.role.get_value().id,
		};
		
		const request_config: AxiosRequestConfig<UserAJAX.Create.IRequestData> = {
			method: "post",
			url: route('ajax.user.create'),
			data: request_data,
		};
		
		return axios.request(request_config).then(
			(response: AxiosResponse<UserAJAX.Create.I200ResponseData>) => {
				// if (response.status >= 300 && response.status < 400) {
				// window.location.replace(route('user.overview'));
				this.success_toast('Benutzer gespeichert');
			},
			() => {
				this.failed_toast('Der Benutzer konnte leider nicht gespeichert werden.');
			}
		);
	}
}
