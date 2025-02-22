import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as LocationAJAX from "@/types/ajax/location";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2 } from "./single/generic/single-value-form2";
import { route } from "ziggy-js";
import { IMultipleValueForm, MultipleValueForm } from "./multiple/multiple-value-form";

export interface ILocationForm {
	get_place_overview_url_path(): string;
	delete(event: any): void;
	init_editing(): void;
	try_save(new_form: LocationForm): void;
	cancel_editing(): void;
	readonly delete_button_enabled: boolean;
};

// um eine zirkuläre Abhängigkeit zu vermeiden:
// locationform.ts (lower module) soll nichts von locationsform.ts (higher module) importieren
export interface ILocationFormParent {
	delete_form(location: LocationForm): void;
	append_form_in_editing(location: LocationForm): void;
}

export interface ILocationFormConstructorArgs {
	data?: {
		id: string,
		name: string,
		is_public: boolean,
	},
	delete_button_enabled?: boolean,
	parent: ILocationFormParent,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
};

export class LocationForm implements ILocationForm {
	public id?: string;
	public name: SingleValueForm2<string, string, true>;
	public is_public: SingleValueForm2<boolean, boolean, true>;
	public delete_button_enabled: boolean;
	
	private readonly parent: ILocationFormParent;
	private readonly toast_service: ToastServiceMethods;
	private readonly confirm_service: ConfirmationServiceMethods;
	
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any> = new MultipleValueForm();
	
	public constructor(args: ILocationFormConstructorArgs) {
		this.parent = args.parent;
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		
		const name_args: ISingleValueForm2ConstructorArgs<string, true> = {
			val: args.data?.name,
			required: true,
		};
		const is_public_args: ISingleValueForm2ConstructorArgs<boolean, true> = {
			val: args.data?.is_public,
			required: true,
		};
		this.id = args.data?.id;
		this.name = new SingleValueForm2(name_args, 'name', this.fields);
		this.is_public = new SingleValueForm2(is_public_args, 'is_public', this.fields);
		this.delete_button_enabled = args.delete_button_enabled ?? true;
	}
	
	private exists_in_db(): boolean {
		return this.id !== undefined;
	}

	public init_editing(): void {
		console.log('LocationForm::cancel_editing()');
		console.log(`this.id === ${this.id}`);
		console.log(`this.name.val === ${this.name.get_value()}`);
		this.delete_button_enabled = false;
	}
	
	public async try_save(new_form: Pick<LocationForm, 'name'|'is_public'>): Promise<void> {
		return new Promise(async (resolve: () => void, reject: () => void) => {
			console.log('LocationForm::on_row_edit_save()');
			console.log(`this.name.val === ${this.name.get_value()}`);
			console.log(`this.name.val_in_editing === ${this.name.get_value_in_editing()}`);
			// this ist direkt das Objekt in der rows-Property oder ein Proxy darauf.
			
			// Die bearbeitete Zeile wird automatisch aus editing_rows entfernt;
			
			if (!this.name.get_value_in_editing()) {
				this.toast_service.add({ severity: 'error', summary: 'Name notwendig', detail: 'Das Feld "Name" darf nicht leer sein', life: 3000 });
				
				this.parent.append_form_in_editing(this);
				// Es muss das IDENTISCHE Zeilen-Objekt in editing_rows erhalten blieben
				// (newData ist ein Kopie und damit zwar gleich aber nicht identisch.)
				reject();
				return;
			}
			
			// Werte der Zeile im Objekt rows setzen:
			this.name.commit();
			this.is_public.commit();

			await this.save();
			
			this.delete_button_enabled = true;
			console.log("Ende");
			resolve();
		});
	}
	
	public cancel_editing(): void {
		console.log('LocationForm::cancel_editing()');
		console.log(`this.id === ${this.id}`);
		console.log(`this.name.val === ${this.name.get_value()}`);
		this.name.rollback();
		this.is_public.rollback();
		if (this.exists_in_db()) {
			// war update
			this.delete_button_enabled = true;
		} else {
			// war create
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
	
	public delete(event: any): Promise<void> {
		return new Promise((resolve: () => void, reject: () => void) => {
			this.confirm_service.require({
				target: event.currentTarget,
				message: "Sind Sie sicher das Sie den Standort löschen wollen? Untergeordnete Plätze werden auch gelöscht.",
				icon: 'pi pi-exclamation-triangle',
				rejectProps: {
					label: 'Abbrechen',
					severity: 'secondary',
					outlined: true
				},
				acceptProps: {
					label: 'Bestätigen'
				},
				accept: () => {
					this.accept_delete().then(resolve, reject)
				},
				reject: reject
			});
		});
	};
	
	private accept_delete(): Promise<void> {
		return new Promise((resolve: () => void, reject: () => void) => {
			this.ajax_delete().then(
				() => {
					this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Standort wurde erfolgreich gelöscht.', life: 3000 });
					this.parent.delete_form(this);
					resolve();
				},
				() => {
					this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Standort konnte nicht gelöscht werden.', life: 3000 });
					reject();
				}
			);
		});
	}
	
	private ajax_create(): Promise<void> {
		const request_config: AxiosRequestConfig<LocationAJAX.Create.IRequestData> = {
			method: "post",
			url: route('ajax.location.create'),
			data: {
				name: this.name.get_value(),
				is_public: this.is_public.get_value(),
			}
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<LocationAJAX.Create.I200ResponseData>) => {
				this.id = response.data
			}
		);
	}
	
	private ajax_update(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<LocationAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.location.update', { location_id: this.id }),
			data: {
				name: this.name.get_value(),
				is_public: this.is_public.get_value(),
			}
		};
		return axios.request(request_config);
	}
	
	private ajax_delete(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		if (!this.exists_in_db()) {
			throw new Error('accept_delete(): Missing id of location');
		}
		const request_config: AxiosRequestConfig<LocationAJAX.Delete.IRequestData> = {
			method: "delete",
			url: route('ajax.location.delete', { location_id: this.id })
		};
		return axios.request(request_config);
	}
	
	public get_place_overview_url_path(): string {
		if (!this.id) {
			throw new Error("undefined id");
		}
		return route('place.overview', { location_id: this.id });
	}
}
