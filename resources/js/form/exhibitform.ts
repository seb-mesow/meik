import { ISingleValueForm2, SingleValueForm2 } from "./singlevalueform2";
import axios, { AxiosRequestConfig } from "axios";
import { route } from "ziggy-js";
import * as ExhibitAJAX from '@/types/ajax/exhibit';
import { ToastServiceMethods } from "primevue/toastservice";

export interface IExhibitForm {
	readonly id?: number;
	readonly inventory_number: Readonly<ISingleValueForm2<string>>;
	readonly name: Readonly<ISingleValueForm2<string>>;
	readonly short_description: Readonly<ISingleValueForm2<string>>;
	readonly rubric: Readonly<ISingleValueForm2<string>>;
	readonly place: Readonly<ISingleValueForm2<string>>;
	readonly manufacturer: Readonly<ISingleValueForm2<string>>;
	click_delete(): void;
	click_save(): void;
}

export interface IExhibitFormFormConstructorArgs {
	id: number,
	inventory_number: string,
	name: string,
	short_description: string,
	rubric: string,
	place: string,
	manufacturer: string,
	toast_service: ToastServiceMethods,
}

export class ExhibitForm implements IExhibitForm {
	public id?: number | undefined;
	public readonly inventory_number: ISingleValueForm2<string>;
	public readonly name: Readonly<ISingleValueForm2<string>>;
	public readonly short_description: ISingleValueForm2<string>;
	public readonly rubric: ISingleValueForm2<string>;
	public readonly place: ISingleValueForm2<string>;
	public readonly manufacturer: Readonly<ISingleValueForm2<string>>;
	
	private readonly toast_service: ToastServiceMethods;
	
	public constructor(args: IExhibitFormFormConstructorArgs) {
		this.id = args?.id;
		this.inventory_number = new SingleValueForm2<string>({ val: args?.inventory_number ?? '' }, 'inventory_number');
		this.name = new SingleValueForm2<string>({ val: args?.name ?? ''}, 'name');
		this.short_description = new SingleValueForm2<string>({ val: args?.short_description ?? '' }, 'short_description');
		this.rubric = new SingleValueForm2<string>({ val: args?.rubric  ?? '' }, 'rubric');
		this.place = new SingleValueForm2<string>({ val: args?.place ?? '' }, 'place');
		this.manufacturer = new SingleValueForm2<string>({ val: args?.manufacturer ?? '' }, 'manufacturer');
		
		this.toast_service = args.toast_service;
	}
	
	private success_toast(msg: string): void {
		this.toast_service.add({ severity: 'success', summary: msg, life: 3000 });
	}
	private failed_toast(msg: string): void {
		this.toast_service.add({ severity: 'error', summary: msg, life: 3000 });
	}
	
	private exists_in_db(): boolean {
		return this.id !== undefined;
	}
	
	public async click_delete(): Promise<void> {
		throw new Error("Method not implemented.");
	}
	
	public async click_save(): Promise<void> {
		this.inventory_number.commit();
		this.name.commit();
		this.short_description.commit();
		this.rubric.commit();
		this.place.commit();
		this.manufacturer.commit();
		
		// this.is_save_button_loading = true;
		if (this.exists_in_db()) {
			await this.ajax_update();
		} else {
			await this.ajax_create();
		}
		// this.is_save_button_loading = false;
	}
	
	private async ajax_update(): Promise<void> {
		if (this.id === undefined) {
			throw new Error("undefined id");
		}
		
		const request_config: AxiosRequestConfig<ExhibitAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.exhibit.update', { exhibit_id: this.id }),
			data: {
				inventory_number: this.inventory_number.get_value(),
				name: this.name.get_value(),
				short_description: this.short_description.get_value(),
				manufacturer: this.manufacturer.get_value(),
			}
		};
		
		return axios.request(request_config).then(
			() => {
				this.success_toast('Exponat gespeichert');
			},
			() => {
				this.failed_toast('Exponat konnte nicht gespeichert werden');
			}
		);
	}
	
	private async ajax_create(): Promise<void> {
		if (this.id !== undefined) {
			throw new Error("defined id");
		}
		
		const request_config: AxiosRequestConfig<ExhibitAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.exhibit.create'),
			data: {
				inventory_number: this.inventory_number.get_value(),
				name: this.name.get_value(),
				short_description: this.short_description.get_value(),
				manufacturer: this.manufacturer.get_value(),
			}
		};
		
		return axios.request(request_config).then(
			() => {
				this.success_toast('neues Exponat gespeichert');
			},
			() => {
				this.success_toast('neues Exponat konnte nicht gespeichert');
			}
		);
	}
}
