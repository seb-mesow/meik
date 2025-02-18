import { ISingleValueForm2, UISingleValueForm2, SingleValueForm2, ISingleValueForm2Parent } from "./single/single-value-form2";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { route } from "ziggy-js";
import * as ExhibitAJAX from '@/types/ajax/exhibit';
import { ToastServiceMethods } from "primevue/toastservice";
import { UISelectForm, SelectForm } from "./single/select-form";
import { GroupSelectForm, UIGroupSelectForm, IGroupType } from "./single/group-select-form";
import { ref, Ref } from "vue";
import { PartialDate } from "@/util/partial-date";
import { PartialDateFrom } from "./single/partialdate-form";
import * as DateUtil from "@/util/date";
import { StringForm } from "./single/string-form";
import { IMultipleValueForm, MultipleValueForm } from "./multiple/multiple-value-form";
import { ICategory, ICategoryWithRubrics, IRubric, RubricForm } from "./single/rubric-form";

export interface IExhibitForm {
	readonly id?: number;
	
	// Kerndaten
	readonly inventory_number: Readonly<UISingleValueForm2>;
	readonly name: Readonly<UISingleValueForm2>;
	readonly short_description: Readonly<UISingleValueForm2>;
	readonly rubric: Readonly<UIGroupSelectForm<IRubric, ICategory>>;
	readonly location: Readonly<UISelectForm<ILocation|undefined>>;
	readonly place: Readonly<UISelectForm<string>>;
	// TODO connected_exhibits
	
	// Bestandsdaten
	readonly preservation_state: Readonly<UISelectForm<IPreservationState>>;
	readonly current_value: Readonly<UISingleValueForm2<number>>;
	readonly kind_of_property: Readonly<UISelectForm<IKindOfProperty>>;
	
	// Zugangsdaten
	readonly acquistion_info: Readonly<{
		readonly date: Readonly<UISingleValueForm2<Date>>;
		readonly source: Readonly<UISingleValueForm2>;
		readonly kind: Readonly<UISelectForm<IKindOfAcquistion>>;
		readonly purchasing_price: Readonly<UISingleValueForm2<number>>;
	}>;
	
	// Exponats-Typ
	readonly type: Readonly<UISingleValueForm2<IExhibitType>>;
	readonly show_device_info: Readonly<Ref<boolean>>;
	readonly show_book_info: Readonly<Ref<boolean>>;
	
	// Geräte- und Buchinformationen
	readonly manufacturer: Readonly<UISingleValueForm2>;
	readonly manufacture_date: Readonly<UISingleValueForm2>;
	readonly original_price: Readonly<{
		readonly amount: Readonly<UISingleValueForm2<number>>;
		readonly currency: Readonly<UISelectForm<ICurrency>>;
	}>;
	
	// Geräteinformationen
	readonly device_info: Readonly<{
		readonly manufactured_from_date: Readonly<UISingleValueForm2>;
		readonly manufactured_to_date: Readonly<UISingleValueForm2>;
	}>;
	
	// Buchinformationen
	readonly book_info: Readonly<{
		readonly authors: Readonly<UISingleValueForm2>;
		readonly language: Readonly<UISelectForm<ILanguage>>;
		readonly isbn: Readonly<UISingleValueForm2>;
	}>;
	
	click_delete(): void;
	click_save(): void;
	
	readonly is_saving_button_enabled: Readonly<Ref<boolean>>;
}

export type ILocation = Readonly<{
	id: string,
	name: string,
}>;
export type IPlace = Readonly<{
	id: string,
	name: string,
}>;
export type IPreservationState = Readonly<{
	id: string,
	name: string,
}>;
export type IKindOfProperty = Readonly<{
	id: string,
	name: string,
}>;
export type IKindOfAcquistion = Readonly<{
	id: string,
	name: string,
}>;
export type IExhibitType = Readonly<{
	id: string,
	name: string,
}>;
export type ICurrency = Readonly<{
	id: string,
	name: string,
}>;
export type ILanguage = Readonly<{
	id: string,
	name: string,
}>;

export type ISelectableValues = Readonly<{
	categories_with_rubrics: ICategoryWithRubrics[],
	location: ILocation[],
	preservation_state: IPreservationState[],
	kind_of_property: IKindOfProperty[],
	kind_of_acquistion: IKindOfAcquistion[],
	exhibit_type: IExhibitType[],
	currency: ICurrency[],
	language: ILanguage[],
}>;

export interface IExhibitFormConstructorArgs {
	data?: {
		id: number,
		
		// Kerndaten
		inventory_number: string,
		name: string,
		short_description?: string, // optional
		rubric: IRubric,
		location_id: string,
		place_id: string,
		// TODO connected_exhibits
		
		// Bestandsdaten
		preservation_state_id: string,
		current_value?: number, // optional
		kind_of_property_id: string,
		
		// Zugangsdaten
		acquistion_info: {
			date: Date,
			source: string,
			kind_id: string,
			purchasing_price?: number, // optional
		},
		
		// Geräte- und Buchinformationen
		manufacturer: string,
		// TODO im Constructor doch erstmal noch string
		manufacture_date?: PartialDate, // optional
		original_price?: { // optional
			amount: number,
			currency_id: string,
		},
		
		// Geräteinformationen
		device_info?: {
			// TODO im Constructor doch erstmal noch string
			manufactured_from_date?: PartialDate, // optional
			manufactured_to_date?: PartialDate, // optional
		}
		
		// Buchinformationen
		book_info?: {
			authors?: string, // optional
			language_id: string,
			isbn?: string, // optional
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
	public readonly inventory_number: SingleValueForm2<string>;
	public readonly name: Readonly<SingleValueForm2<string>>;
	public readonly short_description: SingleValueForm2<string>;
	public readonly rubric: ISingleValueForm2<IRubric> & UIGroupSelectForm<IRubric, ICategory>
	public readonly location: SelectForm<ILocation>;
	public readonly place: SelectForm<string>;
	// TODO connected_exhibits

	// Bestandsdaten
	public readonly preservation_state: Readonly<SelectForm<IPreservationState>>;
	public readonly current_value: Readonly<SingleValueForm2<number, number>>;
	public readonly kind_of_property: Readonly<SelectForm<IKindOfProperty>>;
	
	// Zugangsdaten
	public readonly acquistion_info: Readonly<{
		readonly date: Readonly<SingleValueForm2<Date, Date>>;
		readonly source: Readonly<SingleValueForm2>;
		readonly kind: Readonly<SelectForm<IKindOfAcquistion>>;
		readonly purchasing_price: Readonly<SingleValueForm2<number, number>>;
	}>;
	
	// Exponats-Typ
	public readonly type: Readonly<SingleValueForm2<IExhibitType, IExhibitType>>;
	public show_device_info: Ref<boolean>;
	public show_book_info: Ref<boolean>;
	
	// Geräte- und Buchinformationen
	public readonly manufacturer: Readonly<SingleValueForm2>;
	public readonly manufacture_date: Readonly<SingleValueForm2<PartialDate, string>>;
	public readonly original_price: {
		readonly amount: Readonly<SingleValueForm2<number, number>>;
		readonly currency: Readonly<SelectForm<ICurrency>>;
	};
	
	// Geräteinformationen
	public readonly device_info: {
		readonly manufactured_from_date: Readonly<SingleValueForm2<PartialDate, string>>;
		readonly manufactured_to_date: Readonly<SingleValueForm2<PartialDate, string>>;
	};
	
	// Buchinformationen
	public readonly book_info: {
		readonly authors: Readonly<SingleValueForm2>;
		readonly language: Readonly<SelectForm<ILanguage>>;
		readonly isbn: Readonly<SingleValueForm2>;
	}
	
	// Auswahlwerte
	private readonly selectable_values: ISelectableValues;
	
	// Hilfspbjekte
	private readonly toast_service: ToastServiceMethods;
	private readonly multiple_value_form: IMultipleValueForm & ISingleValueForm2Parent<any>;
	
	// UI-Werte
	public readonly is_saving_button_enabled: Ref<boolean>;
	
	public constructor(args: IExhibitFormConstructorArgs) {
		
		// Auswahlwerte
		this.selectable_values = args.aux.selectable_values;

		// Hilfsobjekte
		this.toast_service = args.aux.toast_service;
		this.multiple_value_form = new MultipleValueForm({
			on_child_change: async (multiple_value_form) => {
				this.is_saving_button_enabled.value = await multiple_value_form.is_valid();
			},
		});
		
		// UI-Werte
		this.is_saving_button_enabled = ref(false);
		
		this.id = args.data?.id;
		
		// Kerndaten
		this.name = new StringForm({
			val: args.data?.name ?? '',
			required: true,
		}, 'name', this.multiple_value_form);
		
		this.inventory_number = new StringForm({
			val: args.data?.inventory_number ?? '',
			required: true,
		}, 'inventory_number', this.multiple_value_form);
		
		
		this.short_description = new StringForm({
			val: args.data?.short_description ?? ''
		}, 'short_description', this.multiple_value_form);
		
		this.rubric = new RubricForm({
			val: args.data?.rubric,
			required: true,
			selectable_categories_with_rubrics: args.aux.selectable_values.categories_with_rubrics,
			// validate(value_in_editing): Promise<string[]> {
			// 	return new Promise<string[]>((resolve) => {
			// 		if (value_in_editing) {
			// 			if (['Rub 1.1', 'Rub 1.2', 'Rub 2.1', 'Rub 2.2'].includes(value_in_editing)) {
			// 				resolve([]);
			// 			} else {
			// 				resolve(['Bitte eine auswählbare Rubrik angeben']);
			// 			}
			// 		} else {
			// 			resolve(['Bitte eine Rubrik angeben']);
			// 		}
			// 	});
			// },
		}, 'rubric', this.multiple_value_form);
		
		this.location = new SelectForm<ILocation>({
			val: this.determinate_selectable_value_from_id(args.data?.location_id ?? '', this.selectable_values.location),
			required: true,
			get_shown_suggestions: (query: string): Promise<ILocation[]> => {
				return this.find_suggestions_in_name(query, this.selectable_values.location);
			},
			validate(value_in_editing): Promise<string[]> {
				throw new Error("not implemented");
			},
		}, 'location', this.multiple_value_form);
		
		this.place = new SelectForm<string>({
			val: args.data?.place_id ?? '',
			required: true,
			get_shown_suggestions(query: string): Promise<string[]> {
				return Promise.resolve([
					'Place 1',
					'Place 2',
					'Place 3',
				])
			},
			validate(value_in_editing): Promise<string[]> {
				throw new Error("not implemented");
			},
		}, 'place', this.multiple_value_form);
		
		// TODO connected_exhibits
		
		// Bestandsdaten
		this.preservation_state = new SelectForm<IPreservationState>({
			val: this.determinate_selectable_value_from_id(args.data?.preservation_state_id ?? '', this.selectable_values.preservation_state),
			required: true,
			get_shown_suggestions: (query: string): Promise<IPreservationState[]> => {
				return this.find_suggestions_in_name(query, this.selectable_values.preservation_state);
			},
			validate: (value_in_editing) => new Promise((resolve) => {
				if (value_in_editing) {
					if (this.selectable_values.preservation_state.some((_selectable_value) => _selectable_value.id === value_in_editing.id)) {
						resolve([]);
					} else {
						resolve(['Bitte einen auswählbaren Erhaltungszustand angeben']);
					}
				} else {
					resolve(['Bitte einen Erhaltungszustand angeben']);
				}
			}),
		}, 'preservation_state', this.multiple_value_form);
		
		this.current_value = new SingleValueForm2<number, number>({
			val: args.data?.current_value ?? 0,
		}, 'current_value', this.multiple_value_form);
		
		this.kind_of_property = new SelectForm<IKindOfProperty>({
			val: this.determinate_selectable_value_from_id(args.data?.kind_of_property_id ?? '', this.selectable_values.kind_of_property),
			required: true,
			get_shown_suggestions: (query: string): Promise<IKindOfProperty[]> => this.find_suggestions_in_name(query, this.selectable_values.kind_of_property),
			validate: (value_in_editing) => new Promise((resolve) => {
				if (value_in_editing) {
					if (this.selectable_values.kind_of_property.some((_selectable_value) => _selectable_value.id === value_in_editing.id)) {
						resolve([]);
					} else {
						resolve(['Bitte eine auswählbare Besitzart angeben']);
					}
				} else {
					resolve(['Bitte eine Besitzart angeben']);
				}
			}),
		}, 'kind_of_property', this.multiple_value_form);
		
		// Zugangsdaten
		this.acquistion_info = {
			date: new SingleValueForm2<Date, Date>({
				val: args.data?.acquistion_info.date ?? new Date(),
				required: true,
				validate: (value_in_editing) => new Promise((resolve) => {
					if (value_in_editing) {
						resolve([]);
					} else {
						resolve(['Bitte ein Datum angeben']);
					}
				}),
			}, 'acquistion_date', this.multiple_value_form),
			
			source: new StringForm({
				val: args.data?.acquistion_info.source ?? '',
				required: true,
			}, 'source', this.multiple_value_form),
			
			kind: new SelectForm<IKindOfAcquistion>({
				val: this.determinate_selectable_value_from_id(args.data?.acquistion_info.kind_id ?? '', this.selectable_values.kind_of_acquistion),
				get_shown_suggestions: (query: string): Promise<IKindOfAcquistion[]> => this.find_suggestions_in_name(query, this.selectable_values.kind_of_acquistion),
				validate: (value_in_editing) => new Promise((resolve) => {
					if (value_in_editing) {
						if (this.selectable_values.kind_of_acquistion.some((_selectable_value) => _selectable_value.id === value_in_editing.id)) {
							resolve([]);
						} else {
							resolve(['Bitte eine auswählbare Zugangsart angeben']);
						}
					} else {
						resolve(['Bitte eine Zugangsart angeben']);
					}
				}),
			}, 'kind_of_acquistion', this.multiple_value_form),
			
			purchasing_price: new SingleValueForm2<number, number>({
				val: args.data?.acquistion_info.purchasing_price ?? 0
			}, 'purchasing_price', this.multiple_value_form),
		};
		
		// Geräte- und Buchinformationen
		this.manufacturer = new StringForm({
			val: args.data?.manufacturer ?? '',
			required: true,
		}, 'manufacturer', this.multiple_value_form);
		
		this.manufacture_date = new PartialDateFrom({
			val: args.data?.manufacture_date
		}, 'manufacture_date', this.multiple_value_form);
		
		this.original_price = {
			amount: new SingleValueForm2<number, number>({
				val: args.data?.original_price?.amount ?? 0
			}, 'original_price_amount', this.multiple_value_form),
			
			currency: new SelectForm<ICurrency>({
				val: this.determinate_selectable_value_from_id(args.data?.original_price?.currency_id ?? '', this.selectable_values.currency),
				get_shown_suggestions: (query: string): Promise<ICurrency[]> => this.find_suggestions_in_id(query, this.selectable_values.currency),
				validate: (value_in_editing) => new Promise((resolve) => {
					if (value_in_editing) {
						if (this.selectable_values.currency.some((_selectable_value) => _selectable_value.id === value_in_editing.id)) {
							resolve([]);
						} else {
							resolve(['Bitte eine auswählbare Währung angeben']);
						}
					} else {
						resolve(['Bitte eine Währung angeben']);
					}
				}),
			}, 'original_price_currency', this.multiple_value_form),
		};
		
		// Geräteinformationen
		const device_info = args.data?.device_info;
		this.device_info = {
			manufactured_from_date: new PartialDateFrom({
				val: device_info?.manufactured_from_date
			}, 'manufactured_from_date', this.multiple_value_form),
			
			manufactured_to_date: new PartialDateFrom({
				val: device_info?.manufactured_to_date
			}, 'manufactured_to_date', this.multiple_value_form),
		}
		
		// Buchinformationen
		const book_info = args.data?.book_info;
		this.book_info = {
			authors: new StringForm({
				val: book_info?.authors ?? ''
			}, 'authors', this.multiple_value_form),
			
			language: new SelectForm<ILanguage>({
				val: this.determinate_selectable_value_from_id(book_info?.language_id ?? '', this.selectable_values.language),
				required: true,
				get_shown_suggestions: (query: string): Promise<ILanguage[]> => this.find_suggestions_in_name(query, this.selectable_values.language),
				validate: (value_in_editing) => new Promise((resolve) => {
					if (value_in_editing) {
						if (this.selectable_values.language.some((_selectable_value) => _selectable_value.id === value_in_editing.id)) {
							resolve([]);
						} else {
							resolve(['Bitte eine auswählbare Währung angeben']);
						}
					} else {
						resolve(['Bitte eine Währung angeben']);
					}
				}),
			}, 'language', this.multiple_value_form),
			
			isbn: new StringForm({
				val: book_info?.isbn ?? ''
			}, 'isbn', this.multiple_value_form),
		};
		
		// Exponats-Typ
		const args_exhibit_type_id: string = (device_info === undefined) ? 'book' : 'device';
		const type: IExhibitType|undefined = args.aux.selectable_values.exhibit_type.find((v) => v.id === args_exhibit_type_id);
		if (!type) {
			throw new Error("exhibit type not found");
		}
		this.show_device_info = ref<boolean>(type.id === 'device');
		this.show_book_info = ref<boolean>(type.id === 'book');
		
		this.type = new SingleValueForm2<IExhibitType, IExhibitType>({
			val: type,
			on_change: (form: ISingleValueForm2<IExhibitType>) => {
				const type = form.get_value_in_editing();
				if (!type) {
					throw new Error("exhibit type not found");
				}
				this.show_device_info.value = type.id === 'device';
				this.show_book_info.value = type.id === 'book';
			},
		}, 'exhibit_type', this.multiple_value_form);
	
	}
	
	private determinate_selectable_value_from_id<T extends { id: string }>(id: string, selectable_values: T[]): T|undefined {
		const found = selectable_values.find((selectable_value: T): boolean => selectable_value.id === id);
		// if (!found) {
		// 	throw new Error(`invalid ID '${id}' for a selectable value`);
		// } 
		return found;
	};
	
	private find_suggestions_in_name<T extends { name: string }>(query: string, selectable_values: T[]): Promise<T[]> {
		// return new Promise((resolve) => {
			query = query.trim();
			if (query.length > 0) {
				query = query.toLowerCase();
				return Promise.resolve(selectable_values.filter((selectable_value: T): boolean => selectable_value.name.toLowerCase().includes(query)));
			} else {
				return Promise.resolve(selectable_values);
			}
	};
	
	private find_suggestions_in_id<T extends { id: string }>(query: string, selectable_values: T[]): Promise<T[]> {
		query = query.toLowerCase();
		return Promise.resolve(
			selectable_values.filter((selectable_value: T): boolean => selectable_value.id.toLowerCase().includes(query))
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
		this.multiple_value_form.commit();
		
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
		
		const request_data: ExhibitAJAX.Update.IRequestData = this.create_or_update_request_data();
		
		const request_config: AxiosRequestConfig<ExhibitAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.exhibit.update', { exhibit_id: this.id }),
			data: request_data,
		};
		
		return axios.request(request_config).then(
			(response) => {
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
		
		const request_data: ExhibitAJAX.Create.IRequestData = this.create_or_update_request_data();
		
		const request_config: AxiosRequestConfig<ExhibitAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.exhibit.create'),
			data: request_data,
		};
		
		return axios.request(request_config).then(
			(response: AxiosResponse<ExhibitAJAX.Create.I200ResponseData>) => {
				this.success_toast(`neues Exponat mit ID ${response.data} gespeichert`);
			},
			() => {
				this.success_toast('neues Exponat konnte nicht gespeichert');
			}
		);
	}
	
	private create_or_update_request_data(): ExhibitAJAX.Create.IRequestData|ExhibitAJAX.Update.IRequestData {
		const request_data: ExhibitAJAX.Create.IRequestData|ExhibitAJAX.Update.IRequestData = {
			inventory_number: this.inventory_number.get_value(),
			name: this.inventory_number.get_value(),
			short_description: this.short_description.get_value(),
			manufacturer: this.manufacturer.get_value(),
			manufacture_date: this.manufacture_date.get_value().format_iso(),
			preservation_state_id: this.preservation_state.get_value().id,
			original_price: {
				amount: this.original_price.amount.get_value(),
				currency_id: this.original_price.currency.get_value().id
			},
			current_value: this.current_value.get_value(),
			acquistion_info: {
				date: DateUtil.format_as_iso_date(this.acquistion_info.date.get_value()),
				source: this.acquistion_info.source.get_value(),
				kind_id: this.acquistion_info.kind.get_value().id,
				purchasing_price: this.acquistion_info.purchasing_price.get_value()
			},
			kind_of_property_id: this.kind_of_property.get_value().id,
			place_id: this.place.get_value(),
			rubric_id: this.rubric.get_value().id,
			// TODO
			conntected_exhibit_ids: [],
		};
		if (this.type.get_value().id === 'device') {
			request_data.device_info = {
				manufactured_from_date: this.device_info.manufactured_from_date.get_value().format_iso(),
				manufactured_to_date: this.device_info.manufactured_to_date.get_value().format_iso(),
			};
		}
		if (this.type.get_value().id === 'book') {
			request_data.book_info = {
				authors: this.book_info.authors.get_value(),
				isbn: this.book_info.isbn.get_value(),
				language_id: this.book_info.language.get_value().id,
			};
		}
		return request_data;
	}
}
