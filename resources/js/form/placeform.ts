import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as PlaceAJAX from "@/types/ajax/place";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "./single/generic/single-value-form2";
import { route } from "ziggy-js";
import { IMultipleValueForm, MultipleValueForm } from "./multiple/multiple-value-form";
import { StringForm } from "./single/generic/string-form";

export interface UIPlaceForm {
	delete(event: any): void;
	readonly id?: string;
	readonly name: Readonly<UISingleValueForm2<string>>;
	readonly delete_button_enabled: boolean;
};

export interface IPlaceForm {
	delete(event: any): void;
	init_editing(): void;
	try_save(): void;
	cancel_editing(): void;
	readonly delete_button_enabled: boolean;
};

// um eine zirkuläre Abhängigkeit zu vermeiden:
// placeform.ts (lower module) soll nichts von placesform.ts (higher module) importieren
export interface IPlaceFormParent {
	delete_form(place: PlaceForm): void;
	append_form_in_editing(place: PlaceForm): void;
	readonly location_id: string;
}

export interface IPlaceFormConstructorArgs {
	data?: {
		id: string,
		name: string,
	},
	delete_button_enabled?: boolean,
	parent: IPlaceFormParent,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
};

export class PlaceForm implements IPlaceForm, UIPlaceForm {
	public id: string|undefined;
	public name: SingleValueForm2<string, string, true>;
	public delete_button_enabled: boolean;
	
	private readonly parent: IPlaceFormParent;
	private readonly toast_service: ToastServiceMethods;
	private readonly confirm_service: ConfirmationServiceMethods;
	
	public readonly dummy_ob_inside = { test: 'shdfsdhjfg'};
	
	// TODO remove
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any> = new MultipleValueForm();
	
	public constructor(args: IPlaceFormConstructorArgs) {
		this.parent = args.parent;
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		
		this.id = args.data?.id;
		this.name = new StringForm<true>({
			val: args.data?.name,
			required: true,
		}, 'name', this.fields);
		this.delete_button_enabled = args.delete_button_enabled ?? true;
	}
	
	private exists_in_db(): boolean {
		return this.id !== undefined;
	}

	public init_editing(): void {
		console.log('PlaceForm::init_editing()');
		console.log(`this.id === ${this.id}`);
		console.log(`this.name.val === ${this.name.get_value()}`);
		console.log(`this.name.ui_value_in_editing ===`);
		console.log(this.name.ui_value_in_editing),
		this.delete_button_enabled = false;
	}
	
	public async try_save(): Promise<void> {
		console.log('PlaceForm::on_row_edit_save()');
		console.log(`this.name.val === ${this.name.get_value()}`);
		console.log(`this.name.val_in_editing === ${this.name.get_value_in_editing()}`);
		// this ist direkt das Objekt in der rows-Property oder ein Proxy darauf.
		
		// Die bearbeitete Zeile wird automatisch aus editing_rows entfernt;
		
		if (!(await this.fields.is_valid())) {
			// this.toast_service.add({ severity: 'error', summary: 'Name notwendig', detail: 'Das Feld "Name" darf nicht leer sein', life: 3000 });
			
			this.parent.append_form_in_editing(this);
			// Es muss das IDENTISCHE Zeilen-Objekt in editing_rows erhalten blieben
			// (newData ist ein Kopie und damit zwar gleich aber nicht identisch.)
			return;
		}
		
		// Werte der Zeile im Objekt rows setzen:
		this.name.commit();

		await this.save();
		
		this.delete_button_enabled = true;
		console.log("Ende");
	}
	
	public async cancel_editing(): Promise<void> {
		console.log('PlaceForm::cancel_editing()');
		console.log(`this.id === ${this.id}`);
		console.log(`this.name.val === ${this.name.get_value()}`);
		
		this.name.rollback();
		
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
				message: "Sind Sie sicher das Sie den Platz löschen wollen?",
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
					this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Platz wurde erfolgreich gelöscht.', life: 3000 });
					this.parent.delete_form(this);
					resolve();
				},
				() => {
					this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Platz konnte nicht gelöscht werden.', life: 3000 });
					reject();
				}
			);
		});
	}
	
	private ajax_create(): Promise<void> {
		const request_config: AxiosRequestConfig<PlaceAJAX.Create.IRequestData> = {
			method: "post",
			url: route('ajax.place.create', { location_id: this.parent.location_id }),
			data: {
				name: this.name.get_value(),
			},
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<PlaceAJAX.Create.I200ResponseData>) => {
				this.id = response.data
			}
		);
	}
	
	private async ajax_update(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		console.log(`PlaceForm::ajax_update(): this.name.get_value() ===`);
		console.log(this.name.get_value());
		const request_config: AxiosRequestConfig<PlaceAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.place.update', { place_id: this.id }),
			data: {
				name: this.name.get_value(),
			},
		};
		try {
			await axios.request(request_config).then(() => {
				this.toast_service.add({ severity: 'info', summary: 'Erfolgreich', detail: 'Der Platz wurde erfolgreich geändert.', life: 3000 });
			});
		} catch (e) {
			this.toast_service.add({ severity: 'error', summary: 'Fehler', detail: 'Der Platz konnte nicht geändert werden.', life: 3000 });
			throw e;
		}
	}
	
	private ajax_delete(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<PlaceAJAX.Delete.IRequestData> = {
			method: "delete",
			url: route('ajax.place.delete', { place_id: this.id })
		};
		return axios.request(request_config);
	}
}
