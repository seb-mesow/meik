import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import {
	ISingleValueFormConstructorArgs,
	SingleValueForm
} from "./singlevalueform";
import {
	ICreateLocation200ResponseData,
	ICreateLocationRequestData,
	IDeleteLocation200ResponseData,
	IUpdateLocationRequestData
} from "@/types/ajax/location";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";

export interface ILocationForm {
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
	id?: string,
	name?: ISingleValueForm2ConstructorArgs<string>,
	is_public?: ISingleValueForm2ConstructorArgs<boolean>,
	delete_button_enabled?: boolean,
	parent: ILocationFormParent,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
};

export class LocationForm implements ILocationForm {
	public id?: string;
	public name: SingleValueForm2<string>;
	public is_public: SingleValueForm2<boolean>;
	public delete_button_enabled: boolean;
	
	private readonly parent: ILocationFormParent;
	private readonly toast_service: ToastServiceMethods;
	private readonly confirm_service: ConfirmationServiceMethods;
	
	public constructor(args: ILocationFormConstructorArgs) {
		this.parent = args.parent;
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		
		const name_args: ISingleValueFormConstructorArgs<string> = {
			val: args.name?.val ?? '',
			errs: args.name?.errs
		};
		const is_public_args: ISingleValueFormConstructorArgs<boolean> = {
			val: args.is_public?.val ?? false,
			errs: args.is_public?.errs
		};
		this.id = args.id;
		this.name = new SingleValueForm2(name_args, 'name');
		this.is_public = new SingleValueForm2(is_public_args, 'is_public');
		this.delete_button_enabled = args.delete_button_enabled ?? true;
	}
	
	private is_persisted(): boolean {
		return this.id !== undefined;
	}

	public init_editing(): void {
		console.log('LocationForm::cancel_editing()');
		console.log(`this.id === ${this.id}`);
		console.log(`this.name.val === ${this.name.val}`);
		this.delete_button_enabled = false;
	}
	
	public async try_save(new_form: Pick<LocationForm, 'name'|'is_public'>): Promise<void> {
		return new Promise(async (resolve: () => void, reject: () => void) => {
			console.log('LocationForm::on_row_edit_save()');
			console.log(`this.name.val === ${this.name.val}`);
			console.log(`this.name.val_in_editing === ${this.name.val_in_editing}`);
			// this ist direkt das Objekt in der rows-Property oder ein Proxy darauf.
			
			// Die bearbeitete Zeile wird automatisch aus editing_rows entfernt;
			
			if (!this.name.val_in_editing) {
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
		console.log(`this.name.val === ${this.name.val}`);
		this.name.rollback();
		this.is_public.rollback();
		if (this.is_persisted()) {
			// war update
			this.delete_button_enabled = true;
		} else {
			// war create
			this.parent.delete_form(this);
		}
	}
	
	private async save(): Promise<void> {
		if (this.is_persisted()) {
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
		const request_config: AxiosRequestConfig<ICreateLocationRequestData> = {
			method: "post",
			url: route('ajax.location.create'),
			data: {
				val: {
					name: {
						val: this.name.val
					},
					is_public: {
						val: this.is_public.val
					}
				}
			}
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<ICreateLocation200ResponseData>) => {
				this.id = response.data
			}
		);
	}
	
	private ajax_update(): Promise<void> {
		const request_config: AxiosRequestConfig<IUpdateLocationRequestData> = {
			method: "put",
			url: route('ajax.location.update', { location_id: this.id }),
			data: {
				val: {
					name: {
						val: this.name.val
					},
					is_public: {
						val: this.is_public.val
					}
				}
			}
		};
		return axios.request(request_config);
	}
	
	private ajax_delete(): Promise<void> {
		if (!this.is_persisted()) {
			throw new Error('accept_delete(): Missing id of location');
		}
		const request_config: AxiosRequestConfig<IDeleteLocation200ResponseData> = {
			method: "delete",
			url: route('ajax.location.delete', { location_id: this.id })
		};
		return axios.request(request_config);
	}
}
