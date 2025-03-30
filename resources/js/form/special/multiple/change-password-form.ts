import { ISingleValueForm2, UISingleValueForm2, ISingleValueForm2Parent } from "../../generic/single/single-value-form2";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { route } from "ziggy-js";
import * as AccountAJAX from '@/types/ajax/account';
import { ToastServiceMethods } from "primevue/toastservice";
import { ref, Ref } from "vue";
import { StringForm } from "../../generic/single/string-form";
import { IMultipleValueForm, MultipleValueForm } from "../../generic/multiple/multiple-value-form";
import { ISimpleSelectForm, StringSimpleSelectForm, UISimpleSelectForm } from "@/form/generic/single/simple-select-form";

export interface UIChangePasswordForm {
	readonly old_password: Readonly<UISingleValueForm2<string>>;
	readonly new_password: Readonly<UISingleValueForm2<string>>;
	readonly new_password_again: Readonly<UISingleValueForm2<string>>;
	
	readonly ui_errs: Readonly<Ref<string[]>>;
	
	click_save(): void;
	
	readonly is_save_button_enabled: Readonly<Ref<boolean>>;
	readonly is_save_button_loading: Readonly<Ref<boolean>>;
}

export interface IChangePasswordConstructorArgs {
	// data?: {
	// },
	
	// // Voreinstellungen
	// preset?: {
	// 	remember: boolean,
	// },
	
	// Auswahlwerte
	// Hilfsobjekte
	aux: {
		toast_service: ToastServiceMethods,
	}
}

export class ChangePasswordForm implements UIChangePasswordForm {
	// Kerndaten
	public readonly old_password: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly new_password: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly new_password_again: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	
	// Hilfspbjekte
	private readonly toast_service: ToastServiceMethods;
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any>;
	
	// UI-Werte
	public readonly ui_errs: Ref<string[], string[]>;
	public readonly is_save_button_enabled: Ref<boolean> = ref(false);
	public readonly is_save_button_loading: Ref<boolean> = ref(false);
	
	private static readonly OLD_PASSWORD_INCORRECT_ERROR_KEY: string = 'not_equal_to_record_in_db';
	
	public constructor(args: IChangePasswordConstructorArgs) {
		// Hilfsobjekte
		this.toast_service = args.aux.toast_service;
		
		this.fields = new MultipleValueForm({
			on_child_change: async (form) => {
				console.log(`ChangePasswordForm: fields: is_valid() == ${await form.is_valid()}`);
				this.is_save_button_enabled.value = await form.is_valid();
			},
		});
		
		// UI-Werte
		this.ui_errs = ref([]);
		
		// Kerndaten
		this.old_password = new StringForm<true>({
			val: undefined,
			required: true,
			on_input_change: async (form) => {
				return form.remove_error(ChangePasswordForm.OLD_PASSWORD_INCORRECT_ERROR_KEY);
			}
		}, 'old_password', this.fields);
		
		this.new_password = new StringForm<true>({
			val: undefined,
			required: true,
			on_input_change: async (form) => {
				if (form.get_value_in_editing() !== this.new_password_again.get_value_in_editing()) {
					return this.new_password_again.add_error('mismatch', 'Die Passwörter stimmen nicht überein.');
				}
				return this.new_password_again.remove_error('mismatch');
			}
		}, 'new_password', this.fields);
		
		this.new_password_again = new StringForm<true>({
			val: undefined,
			required: true,
			on_input_change: async (form) => {
				if (form.get_value_in_editing() !== this.new_password.get_value_in_editing()) {
					return this.new_password_again.add_error('mismatch', 'Die Passwörter stimmen nicht überein.');
				}
				return this.new_password_again.remove_error('mismatch');
			}
		}, 'new_password_again', this.fields);
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
		const request_data: AccountAJAX.ChangePassword.IRequestData = {
			old_password: this.old_password.get_value(),
			new_password: this.new_password.get_value(),
		};
		
		const request_config: AxiosRequestConfig<AccountAJAX.ChangePassword.IRequestData> = {
			method: "patch",
			url: route('ajax.account.change_password'),
			data: request_data,
		};
		
		return axios.request(request_config).then(
			(response: AxiosResponse) => {
				this.success_toast('Ihr Passwort wurde geändert.');
			},
			(response: AxiosResponse) => {
				this.old_password.add_error(ChangePasswordForm.OLD_PASSWORD_INCORRECT_ERROR_KEY, 'Das eingegebene Passwort ist inkorrekt.');
				this.failed_toast('Debug: Fehler');
			}
		);
	}
}
