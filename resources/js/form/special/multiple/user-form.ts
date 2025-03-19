import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as UserAJAX from "@/types/ajax/user";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ISingleValueForm2, ISingleValueForm2Parent, UISingleValueForm2 } from "@/form/generic/single/single-value-form2";
import { route } from "ziggy-js";
import { IMultipleValueForm, MultipleValueForm } from "@/form/generic/multiple/multiple-value-form";
import { ref, Ref } from "vue";
import { StringForm } from "@/form/generic/single/string-form";
import { SelectForm, UISelectForm } from "@/form/generic/single/select-form";
import { ISimpleSelectForm, SimpleSelectForm, StringSimpleSelectForm, UISimpleSelectForm } from "@/form/generic/single/simple-select-form";

export interface UIUserForm {
	request_delete(event: any): void;
	readonly id: Ref<string|undefined>;
	readonly username: Readonly<UISingleValueForm2<string>>;
	readonly forename: Readonly<UISingleValueForm2<string>>;
	readonly surname: Readonly<UISingleValueForm2<string>>;
	readonly role: Readonly<UISimpleSelectForm<IUserRole>>;
	readonly delete_button_enabled: boolean;
};

export type IUserRole = Readonly<{
	id: string,
	name: string,
}>;

export interface IUserForm {
	init_editing(): void;
	try_save(): Promise<void>;
	cancel_editing(): void;
};

// um eine zirkuläre Abhängigkeit zu vermeiden:
// locationform.ts (lower module) soll nichts von locationsform.ts (higher module) importieren
export interface IUserFormParent {
	delete_form(user_form: UserForm): void;
	append_form_in_editing(user_form: UserForm): void;
}

export interface IUserFormConstructorArgs {
	data?: {
		id: string,
		username: string,
		forename: string,
		surname: string,
		role_id: string,
	},
	parent: IUserFormParent,
	// Auswahlwerte
	selectable_values: {
		role: IUserRole[],
	},
	// Hilfsobjekte
	aux: {
		toast_service: ToastServiceMethods,
		confirm_service: ConfirmationServiceMethods,
	}
};

export class UserForm implements IUserForm, UIUserForm {
	public readonly id: Ref<string|undefined>;
	public readonly username: ISingleValueForm2<string, true> & UISingleValueForm2<string>;
	public readonly forename: ISingleValueForm2<string, true> & UISingleValueForm2<string>;
	public readonly surname: ISingleValueForm2<string, true> & UISingleValueForm2<string>;
	public readonly role: ISimpleSelectForm<string, IUserRole, true> & UISimpleSelectForm<IUserRole>;
	public delete_button_enabled: boolean;
	
	private readonly parent: IUserFormParent;
	private readonly toast_service: ToastServiceMethods;
	private readonly confirm_service: ConfirmationServiceMethods;
	
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any> = new MultipleValueForm();
	
	public constructor(args: IUserFormConstructorArgs) {
		this.parent = args.parent;
		this.toast_service = args.aux.toast_service;
		this.confirm_service = args.aux.confirm_service;
		
		this.id = ref(args.data?.id);
		
		this.username = new StringForm<true>({
			val: args.data?.username,
			required: true,
		}, 'username', this.fields);
		
		this.forename = new StringForm<true>({
			val: args.data?.forename,
			required: true,
		}, 'forename', this.fields);
		
		this.surname = new StringForm<true>({
			val: args.data?.surname,
			required: true,
		}, 'surname', this.fields);
		
		this.role = new StringSimpleSelectForm<IUserRole, true>({
			val_id: args.data?.role_id,
			selectable_options: args.selectable_values.role,
			required: true,
			validate: async (value_in_editing) => {
				if (value_in_editing == undefined) {
					return ['Bitte eine auswählbare Benutzerrolle angeben'];
				}
				return [];
			},
		}, 'role', this.fields);
		
		this.delete_button_enabled = true;
		
		if (args.data === undefined) {
			this.init_editing();
		}
	}
	
	private exists_in_db(): boolean {
		return this.id.value !== undefined;
	}

	public init_editing(): void {
		console.log('UserForm::init_editing()');
	}
	
	/**
	 * @throws `Error` if form is invalid
	 */
	public async try_save(): Promise<void> {
		console.log('UserForm::try_save()');
		// console.log(`this.name.val === ${this.name.get_value()}`);
		// console.log(`this.name.val_in_editing === ${this.name.get_value_in_editing()}`);
		// this ist direkt das Objekt in der rows-Property oder ein Proxy darauf.
		
		// Die bearbeitete Zeile wird automatisch aus editing_rows entfernt;
		
		if (!(await this.fields.is_valid())) {
			this.fields.consider();
			
			// this.toast_service.add({ severity: 'error', summary: 'Name notwendig', detail: 'Das Feld "Name" darf nicht leer sein', life: 3000 });
			
			this.parent.append_form_in_editing(this);
			// Es muss das IDENTISCHE Zeilen-Objekt in editing_rows erhalten blieben
			// (newData ist ein Kopie und damit zwar gleich aber nicht identisch.)
			throw new Error('UserForm is invalid.');
		}
		
		// Werte der Zeile im Objekt rows setzen:
		this.fields.commit();
		
		await this.save();
		
		this.delete_button_enabled = true;
		console.log("Ende");
	}
	
	public cancel_editing(): void {
		console.log('UserForm::cancel_editing()');
		console.log(`this.id === ${this.id.value}`);
		
		this.fields.rollback();
		
		if (this.exists_in_db()) {
			// was update
			this.delete_button_enabled = true;
		} else {
			// was create
			this.parent.delete_form(this);
		}
	}
	
	private async save(): Promise<void> {
		if (this.exists_in_db()) {
			return this.ajax_update();
		} else {
			return this.ajax_create();
		}
	}
	
	public request_delete(event: any): void {
		if (!this.exists_in_db()) {
			this.parent.delete_form(this);
			return;
		}
		
		this.confirm_service.require({
			target: event.currentTarget,
			message: "Sind Sie sicher, dass Sie den Benutzer löschen wollen?",
			icon: 'pi pi-exclamation-triangle',
			rejectProps: {
				label: 'Abbrechen',
				severity: 'secondary',
				outlined: true,
			},
			acceptProps: {
				label: 'Bestätigen'
			},
			accept: () => {
				this.accept_delete();
			},
		});
	};
	
	private accept_delete(): Promise<void> {
		return new Promise((resolve: () => void, reject: () => void) => {
			this.ajax_delete().then(() => {
				this.parent.delete_form(this);
				resolve();
			}, () => {
				reject();
			});
		});
	}
	
	private ajax_create(): Promise<void> {
		console.log(`UserForm::ajax_create(): begin`)
		const request_config: AxiosRequestConfig<UserAJAX.Create.IRequestData> = {
			method: "post",
			url: route('ajax.user.create'),
			data: {
				username: this.username.get_value(),
				forename: this.forename.get_value(),
				surname: this.surname.get_value(),
				password: 'TODO',
				role_id: this.role.get_value().id,
			},
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<UserAJAX.Create.I200ResponseData>) => {
				console.log(`UserForm::ajax_create(): response.data ===`)
				console.log(response.data);
				this.id.value = response.data;
				this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Benutzer wurde erfolgreich angelegt.', life: 3000 });
			}, () => {
				this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Benutzer konnte nicht angelegt werden.', life: 3000 });
			},
		);
	}
	
	private ajax_update(): Promise<void> {
		console.log(`UserForm::ajax_update(): begin`);
		if (!this.id.value) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<UserAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.user.update', { user_id: this.id.value }),
			data: {
				username: this.username.get_value(),
				forename: this.forename.get_value(),
				surname: this.surname.get_value(),
				role_id: this.role.get_value().id,
			}
		};
		return axios.request(request_config).then(() => {
			this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Benutzer wurde erfolgreich geändert.', life: 3000 });
		}, () => {
			this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Benutzer konnte nicht geändert werden.', life: 3000 });
		});
	}
	
	private ajax_delete(): Promise<void> {
		console.log(`UserForm::ajax_delete(): begin`);
		if (!this.id.value) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<UserAJAX.Delete.IRequestData> = {
			method: "delete",
			url: route('ajax.user.delete', { user_id: this.id.value })
		};
		
		return axios.request(request_config).then(() => {
			this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Benutzer wurde erfolgreich gelöscht.', life: 3000 });
		}, () => {
			this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Benutzer konnte nicht gelöscht werden.', life: 3000 });
		})
	}
	
	// public get_place_overview_url_path(): string {
	// 	if (!this.id.value) {
	// 		throw new Error("undefined id");
	// 	}
	// 	return route('place.overview', { location_id: this.id.value });
	// }
}
