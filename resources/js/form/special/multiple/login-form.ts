import { ISingleValueForm2, UISingleValueForm2, SingleValueForm2, ISingleValueForm2Parent } from "../../generic/single/single-value-form2";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { route } from "ziggy-js";
import * as LoginAJAX from '@/types/ajax/login';
import { ToastServiceMethods } from "primevue/toastservice";
import { ref, Ref } from "vue";
import { StringForm } from "../../generic/single/string-form";
import { IMultipleValueForm, MultipleValueForm } from "../../generic/multiple/multiple-value-form";

export interface ILoginForm {
	readonly username: Readonly<UISingleValueForm2<string>>;
	readonly password: Readonly<UISingleValueForm2<string>>;
	readonly remember: Readonly<UISingleValueForm2<boolean>>;
	readonly ui_errs: Readonly<Ref<string[]>>;
	
	click_login(): void;
	
	readonly is_login_button_enabled: Readonly<Ref<boolean>>;
	readonly is_login_button_loading: Readonly<Ref<boolean>>;
}

export interface ILoginFormConstructorArgs {
	data?: {
	},
	
	// Voreinstellungen
	preset?: {
		remember: boolean,
	},
	
	aux: {
		// Hilfsobjekte
		toast_service: ToastServiceMethods,
	}
}

export class LoginForm implements ILoginForm {
	// Kerndaten
	public readonly username: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly password: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly remember: Readonly<ISingleValueForm2<boolean, false> & UISingleValueForm2<boolean>>;
	
	// Hilfspbjekte
	private readonly toast_service: ToastServiceMethods;
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any>;
	
	// UI-Werte(false)
	public readonly ui_errs: Ref<string[], string[]>;
	public readonly is_login_button_enabled: Ref<boolean> = ref(false);
	public readonly is_login_button_loading: Ref<boolean> = ref(false);
	
	public constructor(args: ILoginFormConstructorArgs) {
		// Hilfsobjekte
		this.toast_service = args.aux.toast_service;
		this.fields = new MultipleValueForm({
			on_child_change: async (form) => {
				this.is_login_button_enabled.value = await form.is_valid();
			},
		});
		
		// UI-Werte
		this.ui_errs = ref([]);
		
		// Kerndaten
		this.username = new StringForm<true>({
			val: undefined,
			required: true,
		}, 'username', this.fields);
		
		this.password = new StringForm<true>({
			val: undefined,
			required: true,
		}, 'password', this.fields);
		
		this.remember = new SingleValueForm2<boolean, boolean, false>({
			val: args.preset?.remember ?? false,
			required: false,
		}, 'password', this.fields);
	}
	
	private success_toast(msg: string): void {
		this.toast_service.add({ severity: 'success', summary: msg, life: 3000 });
	}
	
	private failed_toast(msg: string): void {
		this.toast_service.add({ severity: 'error', summary: msg, life: 3000 });
	}
	
	public async click_login(): Promise<void> {
		this.ui_errs.value = [];
		this.fields.commit();
		
		this.is_login_button_loading.value = true;
		await this.ajax_login();
		this.is_login_button_loading.value = false;
	}
	
	private async ajax_login(): Promise<void> {
		const request_data: LoginAJAX.Login.IRequestDate = {
			username: this.username.get_value(),
			password: this.password.get_value(),
			remember: this.remember.get_value() ?? false,
		};
		
		const request_config: AxiosRequestConfig<LoginAJAX.Login.IRequestDate> = {
			method: "post",
			url: route('ajax.user.login'),
			data: request_data,
		};
		
		return axios.request(request_config).then(
			(response: AxiosResponse<LoginAJAX.Login.I200ResponseData>) => {
				// if (response.status >= 300 && response.status < 400) {
				window.location.replace(route('category.overview'));
				// this.success_toast('Der Login war erfolgreich');
			},
			() => {
				this.failed_toast('Der Login war leider nicht möglich.');
				this.ui_errs.value = ['Der Benutzername oder das Passwort sind falsch.'];
			}
		);
	}
}
