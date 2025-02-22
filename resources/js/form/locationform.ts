import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as LocationAJAX from "@/types/ajax/location";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "./single/generic/single-value-form2";
import { route } from "ziggy-js";
import { IMultipleValueForm, MultipleValueForm } from "./multiple/multiple-value-form";
import { ref, Ref } from "vue";
import { StringForm } from "./single/generic/string-form";

export interface UILocationForm {
	request_delete(event: any): void;
	readonly id: Ref<string|undefined>;
	readonly name: Readonly<UISingleValueForm2<string>>;
	readonly is_public: Readonly<UISingleValueForm2<boolean>>;
	readonly delete_button_enabled: boolean;
	get_place_overview_url_path(): string;
};

export interface ILocationForm {
	init_editing(): void;
	try_save(): Promise<void>;
	cancel_editing(): void;
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
	parent: ILocationFormParent,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
};

export class LocationForm implements ILocationForm, UILocationForm {
	public readonly id: Ref<string|undefined>;
	public readonly name: SingleValueForm2<string, string, true>;
	public readonly is_public: SingleValueForm2<boolean, boolean, false>;
	public delete_button_enabled: boolean;
	
	private readonly parent: ILocationFormParent;
	private readonly toast_service: ToastServiceMethods;
	private readonly confirm_service: ConfirmationServiceMethods;
	
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any> = new MultipleValueForm();
	
	public constructor(args: ILocationFormConstructorArgs) {
		this.parent = args.parent;
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		
		this.id = ref(args.data?.id);
		this.name = new StringForm<true>({
			val: args.data?.name,
			required: true,
		}, 'name', this.fields);
		this.is_public = new SingleValueForm2<boolean, boolean, false>({
			val: args.data?.is_public,
			required: false,
		}, 'is_public', this.fields);
		
		this.delete_button_enabled = true;
		if (args.data === undefined) {
			this.init_editing();
		}
	}
	
	private exists_in_db(): boolean {
		return this.id.value !== undefined;
	}

	public init_editing(): void {
		console.log('LocationForm::init_editing()');
	}
	
	/**
	 * @throws `Error` if form is invalid
	 */
	public async try_save(): Promise<void> {
		console.log('LocationForm::try_save()');
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
			throw new Error('LocationForm is invalid.');
		}
		
		// Werte der Zeile im Objekt rows setzen:
		this.fields.commit();
		
		await this.save();
		
		this.delete_button_enabled = true;
		console.log("Ende");
	}
	
	public cancel_editing(): void {
		console.log('LocationForm::cancel_editing()');
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
			message: "Sind Sie sicher das Sie den Standort löschen wollen? Untergeordnete Plätze werden auch gelöscht.",
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
		console.log(`PlaceForm::ajax_create(): begin`)
		const request_config: AxiosRequestConfig<LocationAJAX.Create.IRequestData> = {
			method: "post",
			url: route('ajax.location.create'),
			data: {
				name: this.name.get_value(),
				is_public: this.is_public.get_value() ?? false,
			},
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<LocationAJAX.Create.I200ResponseData>) => {
				console.log(`PlaceForm::ajax_create(): response.data ===`)
				console.log(response.data);
				this.id.value = response.data;
				this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Standort wurde erfolgreich angelegt.', life: 3000 });
			}, () => {
				this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Standort konnte nicht angelegt werden.', life: 3000 });
			},
		);
	}
	
	private ajax_update(): Promise<void> {
		console.log(`PlaceForm::ajax_update(): begin`);
		if (!this.id.value) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<LocationAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.location.update', { location_id: this.id.value }),
			data: {
				name: this.name.get_value(),
				//@ts-expect-error
				is_public: this.is_public.get_value(),
			}
		};
		return axios.request(request_config).then(() => {
			this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Standort wurde erfolgreich geändert.', life: 3000 });
		}, () => {
			this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Standort konnte nicht geändert werden.', life: 3000 });
		});
	}
	
	private ajax_delete(): Promise<void> {
		console.log(`LocationForm::ajax_delete(): begin`);
		if (!this.id.value) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<LocationAJAX.Delete.IRequestData> = {
			method: "delete",
			url: route('ajax.location.delete', { location_id: this.id.value })
		};
		
		return axios.request(request_config).then(() => {
			this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Standort wurde erfolgreich gelöscht.', life: 3000 });
		}, () => {
			this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Standort konnte nicht gelöscht werden.', life: 3000 });
		})
	}
	
	public get_place_overview_url_path(): string {
		if (!this.id.value) {
			throw new Error("undefined id");
		}
		return route('place.overview', { location_id: this.id.value });
	}
}
