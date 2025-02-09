import { ISingleValueForm2, SingleValueForm2 } from "./singlevalueform2";
import axios, { AxiosRequestConfig } from "axios";
import { route } from "ziggy-js";
import * as ExhibitAJAX from '@/types/ajax/exhibit';
import { ToastServiceMethods } from "primevue/toastservice";
import { ISelectForm, SelectForm } from "./selectform";
import { GroupSelectForm, IGroupSelectForm, IGroupType } from "./groupselectform";
import { ref, Ref } from "vue";

export interface IExhibitForm {
	readonly id?: number;
	
	// Kerndaten
	readonly inventory_number: Readonly<ISingleValueForm2>;
	readonly name: Readonly<ISingleValueForm2>;
	readonly short_description: Readonly<ISingleValueForm2>;
	readonly rubric: Readonly<IGroupSelectForm>;
	readonly location: Readonly<ISelectForm<ILocation|undefined>>;
	readonly place: Readonly<ISelectForm>;
	// TODO connected_exhibits
	
	// Bestandsdaten
	readonly preservation_state: Readonly<ISelectForm<IPreservationState>>;
	readonly current_value: Readonly<ISingleValueForm2<number>>;
	readonly kind_of_property: Readonly<ISelectForm<IKindOfProperty>>;
	
	// Zugangsdaten
	readonly acquistion_date: Readonly<ISingleValueForm2>;
	readonly source: Readonly<ISingleValueForm2>;
	readonly kind_of_acquistion: Readonly<ISelectForm<IKindOfAcquistion>>;
	readonly purchasing_price: Readonly<ISingleValueForm2<number>>;
	
	// Exponats-Typ
	readonly type: Readonly<ISingleValueForm2<IExhibitType>>;
	readonly show_device_info: Readonly<Ref<boolean>>;
	readonly show_book_info: Readonly<Ref<boolean>>;
	
	// Geräte- und Buchinformationen
	readonly manufacturer: Readonly<ISingleValueForm2>;
	readonly original_price_amount: Readonly<ISingleValueForm2<number>>;
	readonly original_price_currency: Readonly<ISelectForm<ICurrency>>;
	
	// Geräteinformationen
	readonly device_info: {
		readonly manufactured_from_date: Readonly<ISingleValueForm2>;
		readonly manufactured_to_date: Readonly<ISingleValueForm2>;
	}
	
	// Buchinformationen
	readonly book_info: {
		readonly authors: Readonly<ISingleValueForm2>;
		readonly language: Readonly<ISelectForm<ILanguage>>;
		readonly isbn: Readonly<ISingleValueForm2>;
	}
	
	click_delete(): void;
	click_save(): void;
}

export type ICurrency = Readonly<{
	id: string,
	name: string,
}>;
export type IKindOfAcquistion = Readonly<{
	id: string,
	name: string,
}>;
export type IKindOfProperty = Readonly<{
	id: string,
	name: string,
}>;
export type ILanguage = Readonly<{
	id: string,
	name: string,
}>;
export type IPreservationState = Readonly<{
	id: string,
	name: string,
}>;
export type ILocation = Readonly<{
	id: string,
	name: string,
}>;
export type IExhibitType = Readonly<{
	id: string,
	name: string,
}>;

export type ISelectableValues = Readonly<{
	currency: ICurrency[],
	kind_of_acquistion: IKindOfAcquistion[],
	kind_of_property: IKindOfProperty[],
	language: ILanguage[],
	preservation_state: IPreservationState[],
	location: ILocation[],
	exhibit_type: IExhibitType[],
}>;

export interface IExhibitFormConstructorArgs {
	data?: {
		id: number,
		
		// Kerndaten
		inventory_number: string,
		name: string,
		short_description: string,
		rubric: string,
		location_id: string,
		place_id: string,
		// TODO connected_exhibits
		
		// Bestandsdaten
		preservation_state_id: string,
		current_value: number,
		kind_of_property_id: string,
		
		// Zugangsdaten
		acquistion_info: {
			date: string, // TODO should be a Date object
			source: string,
			kind_id: string,
			purchasing_price: number,
		},
		
		// Geräte- und Buchinformationen
		manufacturer: string,
		original_price: {
			amount: number,
			currency_id: string,
		},
		
		// Geräteinformationen
		device_info?: {
			manufactured_from_date: string,
			manufactured_to_date: string,
		}
		
		// Buchinformationen
		book_info?: {
			authors: string,
			language_id: string,
			isbn: string,
		}
	},
	
	aux: {
		// Auswahlwerte
		selectable_values: ISelectableValues,
		
		// Hilfsobjekte
		toast_service: ToastServiceMethods,
	}
}

export class ExhibitForm implements IExhibitForm {
	public id?: number | undefined;
	
	// Kerndaten
	public readonly inventory_number: ISingleValueForm2<string>;
	public readonly name: Readonly<ISingleValueForm2<string>>;
	public readonly short_description: ISingleValueForm2<string>;
	public readonly rubric: IGroupSelectForm<string>;
	public readonly location: ISelectForm<ILocation|undefined>;
	public readonly place: ISelectForm<string>;
	// TODO connected_exhibits

	// Bestandsdaten
	public readonly preservation_state: Readonly<ISelectForm<IPreservationState>>;
	public readonly current_value: Readonly<ISingleValueForm2<number>>;
	public readonly kind_of_property: Readonly<ISelectForm<IKindOfProperty>>;
	
	// Zugangsdaten
	public readonly acquistion_date: Readonly<ISingleValueForm2>;
	public readonly source: Readonly<ISingleValueForm2>;
	public readonly kind_of_acquistion: Readonly<ISelectForm<IKindOfAcquistion>>;
	public readonly purchasing_price: Readonly<ISingleValueForm2<number>>;
	
	// Exponats-Typ
	public readonly type: Readonly<ISingleValueForm2<IExhibitType>>;
	public show_device_info: Ref<boolean>;
	public show_book_info: Ref<boolean>;
	
	// Geräte- und Buchinformationen
	public readonly manufacturer: Readonly<ISingleValueForm2>;
	public readonly original_price_amount: Readonly<ISingleValueForm2<number>>;
	public readonly original_price_currency: Readonly<ISelectForm<ICurrency>>;
	
	// Geräteinformationen
	public device_info: {
		readonly manufactured_from_date: Readonly<ISingleValueForm2>;
		readonly manufactured_to_date: Readonly<ISingleValueForm2>;
	};
	
	// Buchinformationen
	public book_info: {
		readonly authors: Readonly<ISingleValueForm2>;
		readonly language: Readonly<ISelectForm<ILanguage>>;
		readonly isbn: Readonly<ISingleValueForm2>;
	}
	
	private readonly selectable_values: ISelectableValues;
	private readonly toast_service: ToastServiceMethods;
	
	public constructor(args: IExhibitFormConstructorArgs) {
		// Auswahlwerte
		this.selectable_values = args.aux.selectable_values;

		// Hilfsobjekte
		this.toast_service = args.aux.toast_service;
		
		this.id = args.data?.id;
		
		// Kerndaten
		this.inventory_number = new SingleValueForm2({ val: args.data?.inventory_number ?? '' }, 'inventory_number');
		
		this.name = new SingleValueForm2({ val: args.data?.name ?? '' }, 'name');
		
		this.short_description = new SingleValueForm2({ val: args.data?.short_description ?? '' }, 'short_description');
		
		this.rubric = new GroupSelectForm<string>({
			val: args.data?.rubric ?? '',
			get_shown_suggestions(query: string): Promise<IGroupType[]> {
				return Promise.resolve([
					{
						parent: 'Kat 1',
						children: [
							'Rub 1.1',
							'Rub 1.2',
						]
					},
					{
						parent: 'Kat 2',
						children: [
							'Rub 2.1',
							'Rub 2.2',
						]
					}
				])
			}
		}, 'rubric');
		
		this.location = new SelectForm<ILocation>({
			val: this.determinate_selectable_value_from_id(args.data?.location_id ?? '', this.selectable_values.location),
			get_shown_suggestions: (query: string): Promise<ILocation[]> => this.find_suggestions(query, this.selectable_values.location),
		}, 'location');
		
		this.place = new SelectForm({
			val: args.data?.place_id ?? '',
			get_shown_suggestions(query: string): Promise<string[]> {
				return Promise.resolve([
					'Place 1',
					'Place 2',
					'Place 3',
				])
			},
		}, 'place');
		
		// TODO connected_exhibits
		
		// Bestandsdaten
		this.preservation_state = new SelectForm<IPreservationState>({
			val: this.determinate_selectable_value_from_id(args.data?.preservation_state_id ?? '', this.selectable_values.preservation_state),
			get_shown_suggestions: (query: string): Promise<IPreservationState[]> => this.find_suggestions(query, this.selectable_values.preservation_state),
		}, 'preservation_state');
		
		this.current_value = new SingleValueForm2<number>({ val: args.data?.current_value ?? 0}, 'current_value');
		
		this.kind_of_property = new SelectForm<IKindOfProperty>({
			val: this.determinate_selectable_value_from_id(args.data?.kind_of_property_id ?? '', this.selectable_values.kind_of_property),
			get_shown_suggestions: (query: string): Promise<IKindOfProperty[]> => this.find_suggestions(query, this.selectable_values.kind_of_property),
		}, 'kind_of_property');
		
		// Zugangsdaten
		this.acquistion_date = new SingleValueForm2({ val: args.data?.acquistion_info.date ?? '9999-12-31' }, 'acquistion_date');
		
		this.source = new SingleValueForm2({ val: args.data?.acquistion_info.source ?? '' }, 'source');
		
		this.kind_of_acquistion = new SelectForm<IKindOfAcquistion>({
			val: this.determinate_selectable_value_from_id(args.data?.acquistion_info.kind_id ?? '', this.selectable_values.kind_of_acquistion),
			get_shown_suggestions: (query: string): Promise<IKindOfAcquistion[]> => this.find_suggestions(query, this.selectable_values.kind_of_acquistion),
		}, 'kind_of_acquistion');
		
		this.purchasing_price = new SingleValueForm2<number>({ val: args.data?.acquistion_info.purchasing_price ?? 0 }, 'purchasing_price');
		
		// Geräte- und Buchinformationen
		this.manufacturer = new SingleValueForm2({ val: args.data?.manufacturer ?? '' }, 'manufacturer');
		
		this.original_price_amount = new SingleValueForm2<number>({ val: args.data?.original_price.amount ?? 0 }, 'original_price_amount');
		
		this.original_price_currency = new SelectForm<ICurrency>({
			val: this.determinate_selectable_value_from_id(args.data?.original_price.currency_id ?? '', this.selectable_values.currency),
			get_shown_suggestions: (query: string): Promise<ICurrency[]> => this.find_suggestions(query, this.selectable_values.currency),
		}, 'original_price_currency');
		
		// Geräteinformationen
		const device_info = args.data?.device_info;
		this.device_info = {
			manufactured_from_date: new SingleValueForm2({ val: device_info?.manufactured_from_date ?? '' }, 'manufactured_from_date'),
			
			manufactured_to_date: new SingleValueForm2({ val: device_info?.manufactured_to_date ?? '' }, 'manufactured_to_date'),
		}
		
		// Buchinformationen
		const book_info = args.data?.book_info;
		this.book_info = {
			authors: new SingleValueForm2({ val: book_info?.authors ?? '' }, 'authors'),
			
			language: new SelectForm<ILanguage>({
				val: this.determinate_selectable_value_from_id(book_info?.language_id ?? '', this.selectable_values.language),
				get_shown_suggestions: (query: string): Promise<ILanguage[]> => this.find_suggestions(query, this.selectable_values.language),
			}, 'original_price_currency'),
			
			isbn: new SingleValueForm2({ val: book_info?.isbn ?? '' }, 'isbn'),
		};
		
		// Exponats-Typ
		const is_device: boolean = book_info === undefined;
		const type: IExhibitType|undefined = args.aux.selectable_values.exhibit_type.find((v) => v.id === (is_device ? 'device' : 'book'));
		if (!type) {
			throw new Error("exhibit type not found");
		}
		this.show_device_info = ref<boolean>(type.id === 'device');
		this.show_book_info = ref<boolean>(type.id === 'book');
		
		this.type = new SingleValueForm2<IExhibitType>({
			val: type,
			on_change: (form: ISingleValueForm2<IExhibitType>) => {
				const type = form.val_in_editing;
				if (!type) {
					throw new Error("exhibit type not found");
				}
				this.show_device_info.value = type.id === 'device';
				this.show_book_info.value = type.id === 'book';
			},
		}, 'exhibit_type');
	
	}
	
	private determinate_selectable_value_from_id<T extends { id: string }>(id: string, selectable_values: T[]): T|undefined {
		const found = selectable_values.find((selectable_value: T): boolean => selectable_value.id === id);
		// if (!found) {
		// 	throw new Error(`invalid ID '${id}' for a selectable value`);
		// } 
		return found;
	};
	
	private find_suggestions<T extends { name: string}>(query: string, selectable_values: T[]): Promise<T[]> {
		return Promise.resolve(
			selectable_values.filter((selectable_value: T): boolean => selectable_value.name.includes(query))
		);
	};
	
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
				// TODO Breadcrumbb anpassen
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
