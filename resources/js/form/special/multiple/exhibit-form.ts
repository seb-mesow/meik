import { ISingleValueForm2, UISingleValueForm2, SingleValueForm2, ISingleValueForm2Parent } from "../../generic/single/single-value-form2";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { route } from "ziggy-js";
import * as ExhibitAJAX from '@/types/ajax/exhibit';
import { ToastServiceMethods } from "primevue/toastservice";
import { UISelectForm, SelectForm } from "../../generic/single/select-form";
import { UIGroupSelectForm } from "../../generic/single/group-select-form";
import { ref, Ref } from "vue";
import * as PartialDate from "@/util/partial-date";
import { PartialDateFrom } from "../single/partialdate-form";
import * as DateUtil from "@/util/date";
import { StringForm } from "../../generic/single/string-form";
import { IMultipleValueForm, MultipleValueForm } from "../../generic/multiple/multiple-value-form";
import { ICategory, ICategoryWithRubrics, IRubric, RubricForm } from "../single/rubric-form";
import { ILocation, LocationForm } from "../single/location-form";
import { IPlace, IPlaceForm, PlaceForm } from "../single/place-form";
import { IMultiSelectForm, MultiSelectForm, NumberMultipleSelectForm, UIMultiSelectForm } from "@/form/generic/single/multi-select-form";
import { ConnectedExhibitFrom, IConnectedExhibit } from "../single/connected-exhibit-form";

export interface IExhibitForm {
	readonly id: number|undefined;
	
	// Kerndaten
	readonly inventory_number:  Readonly<UISingleValueForm2<string>>;
	readonly name: Readonly<UISingleValueForm2<string>>;
	readonly short_description: Readonly<UISingleValueForm2<string>>;
	readonly rubric: Readonly<UIGroupSelectForm<IRubric, ICategory>>;
	readonly location: Readonly<UISelectForm<ILocation>>;
	readonly place: Readonly<UISelectForm<IPlace>>;
	readonly connected_exhibits: Readonly<UIMultiSelectForm<IConnectedExhibit>>;
	
	// Bestandsdaten
	readonly preservation_state: Readonly<UISelectForm<IPreservationState>>;
	readonly current_value: Readonly<UISingleValueForm2<number|null>>;
	readonly kind_of_property: Readonly<UISelectForm<IKindOfProperty>>;
	
	// Zugangsdaten
	readonly acquisition_info: Readonly<{
		readonly date: Readonly<UISingleValueForm2<Date>>;
		readonly source: Readonly<UISingleValueForm2<string>>;
		readonly kind: Readonly<UISelectForm<IKindOfAcquisition>>;
		readonly purchasing_price: Readonly<UISingleValueForm2<number|null>>;
	}>;
	
	// Exponats-Typ
	readonly type: Readonly<UISingleValueForm2<IExhibitType>>;
	readonly show_device_info: Readonly<Ref<boolean>>;
	readonly show_book_info: Readonly<Ref<boolean>>;
	
	// Geräte- und Buchinformationen
	readonly manufacturer: Readonly<UISingleValueForm2<string>>;
	readonly manufacture_date: Readonly<UISingleValueForm2>;
	readonly original_price: Readonly<{
		readonly amount: Readonly<UISingleValueForm2<number|null>>;
		readonly currency: Readonly<UISelectForm<ICurrency>>;
	}>;
	
	// Geräteinformationen
	readonly device_info: Readonly<{
		readonly manufactured_from_date: Readonly<UISingleValueForm2<string>>;
		readonly manufactured_to_date: Readonly<UISingleValueForm2<string>>;
	}>;
	
	// Buchinformationen
	readonly book_info: Readonly<{
		readonly authors: Readonly<UISingleValueForm2<string>>;
		readonly language: Readonly<UISelectForm<ILanguage>>;
		readonly isbn: Readonly<UISingleValueForm2<string>>;
	}>;
	
	click_delete(): void;
	click_save(): void;
	
	readonly is_save_button_enabled: Readonly<Ref<boolean>>;
	readonly is_save_button_loading: Readonly<Ref<boolean>>;
}

export type IPreservationState = Readonly<{
	id: string,
	name: string,
}>;
export type IKindOfProperty = Readonly<{
	id: string,
	name: string,
}>;
export type IKindOfAcquisition = Readonly<{
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
	initial_places?: IPlace[],
	preservation_state: IPreservationState[],
	kind_of_property: IKindOfProperty[],
	kind_of_acquisition: IKindOfAcquisition[],
	exhibit_type: IExhibitType[],
	currency: ICurrency[],
	language: ILanguage[],
	initial_connected_exhibits?: IConnectedExhibit[],
}>;

export interface IExhibitFormConstructorArgs {
	data?: {
		id: number,
		
		// Kerndaten
		inventory_number: string,
		name: string,
		short_description: string, // optional
		rubric: IRubric,
		location_id: string,
		place_id: string,
		connected_exhibit_ids: number[],
		
		// Bestandsdaten
		preservation_state_id: string,
		current_value: number|null, // optional
		kind_of_property_id: string,
		
		// Zugangsdaten
		acquisition_info: {
			date: string,
			source: string,
			kind_id: string,
			purchasing_price: number|null, // optional
		},
		
		// Geräte- und Buchinformationen
		manufacturer: string,
		// TODO im Constructor doch erstmal noch string
		manufacture_date: string, // optional
		original_price: { // optional
			amount: number|null,
			currency_id: string,
		},
		
		// Geräteinformationen
		device_info?: {
			manufactured_from_date: string, // optional
			manufactured_to_date: string, // optional
		}
		
		// Buchinformationen
		book_info?: {
			authors: string, // optional
			language_id: string,
			isbn: string, // optional
		}
	},
	
	// Voreinstellungen
	preset?: {
		rubric?: IRubric,
	},
	
	aux: {
		// Auswahlwerte
		selectable_values: ISelectableValues,
		
		// Hilfsobjekte
		toast_service: ToastServiceMethods,
	}
}

export class ExhibitForm implements IExhibitForm {
	public readonly id: number|undefined;
	
	// Kerndaten
	public readonly inventory_number: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly name: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly short_description: Readonly<ISingleValueForm2<string> & UISingleValueForm2<string>>;
	public readonly rubric: ISingleValueForm2<IRubric, true> & UIGroupSelectForm<IRubric, ICategory>
	public readonly location: ISingleValueForm2<ILocation, true> & UISelectForm<ILocation>;
	public readonly place: IPlaceForm<true> & UISelectForm<IPlace>;
	public readonly connected_exhibits: IMultiSelectForm<number, IConnectedExhibit, false> & UIMultiSelectForm<IConnectedExhibit>;

	// Bestandsdaten
	public readonly preservation_state: Readonly<ISingleValueForm2<IPreservationState, true> & UISelectForm<IPreservationState>>;
	public readonly current_value: Readonly<ISingleValueForm2<number> & UISingleValueForm2<number|null>>;
	public readonly kind_of_property: Readonly<ISingleValueForm2<IKindOfProperty, true> & UISelectForm<IKindOfProperty>>;
	
	// Zugangsdaten
	public readonly acquisition_info: Readonly<{
		readonly date: Readonly<ISingleValueForm2<Date, true> & UISingleValueForm2<Date>>;
		readonly source: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
		readonly kind: Readonly<ISingleValueForm2<IKindOfAcquisition> & UISelectForm<IKindOfAcquisition>>;
		readonly purchasing_price: Readonly<ISingleValueForm2<number> & UISingleValueForm2<number|null>>;
	}>;
	
	// Exponats-Typ
	public readonly type: Readonly<SingleValueForm2<IExhibitType, IExhibitType, true>>;
	public show_device_info: Ref<boolean>;
	public show_book_info: Ref<boolean>;
	
	// Geräte- und Buchinformationen
	public readonly manufacturer: Readonly<ISingleValueForm2<string, true> & UISingleValueForm2<string>>;
	public readonly manufacture_date: Readonly<ISingleValueForm2<PartialDate.PartialDate> & UISingleValueForm2<string>>;
	public readonly original_price: {
		readonly amount: Readonly<ISingleValueForm2<number> & UISingleValueForm2<number|null>>;
		readonly currency: Readonly<ISingleValueForm2<ICurrency, boolean> & UISelectForm<ICurrency>>;
	};
	
	// Geräteinformationen
	public readonly device_info: {
		readonly manufactured_from_date: Readonly<ISingleValueForm2<PartialDate.PartialDate> & UISingleValueForm2<string>>;
		readonly manufactured_to_date: Readonly<ISingleValueForm2<PartialDate.PartialDate> & UISingleValueForm2<string>>;
	};
	
	// Buchinformationen
	public readonly book_info: {
		readonly authors: Readonly<ISingleValueForm2<string> & UISingleValueForm2<string>>;
		readonly language: Readonly<ISingleValueForm2<ILanguage, true> & UISelectForm<ILanguage>>;
		readonly isbn: Readonly<ISingleValueForm2<string> & UISingleValueForm2<string>>;
	}
	
	// Auswahlwerte
	private readonly selectable_values: ISelectableValues;
	
	// Hilfspbjekte
	private readonly toast_service: ToastServiceMethods;
	private readonly common_fields: IMultipleValueForm & ISingleValueForm2Parent<any>;
	private readonly device_fields: IMultipleValueForm & ISingleValueForm2Parent<any>;
	private readonly book_fields: IMultipleValueForm & ISingleValueForm2Parent<any>;
	
	// UI-Werte(false)
	public readonly is_save_button_enabled: Ref<boolean> = ref(false);
	public readonly is_save_button_loading: Ref<boolean> = ref(false);
	
	public constructor(args: IExhibitFormConstructorArgs) {
		
		// Auswahlwerte
		this.selectable_values = args.aux.selectable_values;

		// Hilfsobjekte
		this.toast_service = args.aux.toast_service;
		this.common_fields = new MultipleValueForm({
			on_child_change: (form) => this.on_child_field_change(form),
		});
		this.device_fields = new MultipleValueForm({
			on_child_change: (form) => this.on_child_field_change(form),
		});
		this.book_fields = new MultipleValueForm({
			on_child_change: (form) => this.on_child_field_change(form),
		});
		
		// UI-Werte
		
		this.id = args.data?.id;
		
		// Kerndaten
		this.name = new StringForm<true>({
			val: args.data?.name,
			required: true,
		}, 'name', this.common_fields);
		
		this.inventory_number = new StringForm<true>({
			val: args.data?.inventory_number,
			required: true,
		}, 'inventory_number', this.common_fields);
		
		
		this.short_description = new StringForm({
			val: args.data?.short_description,
			required: false,
		}, 'short_description', this.common_fields);
		
		this.rubric = new RubricForm<true>({
			val: args.data?.rubric ?? args.preset?.rubric,
			required: true,
			selectable_categories_with_rubrics: args.aux.selectable_values.categories_with_rubrics,
			validate: async (value_in_editing) => {
				if (value_in_editing === undefined) {
					return ['Bitte eine auswählbare Rubrik angeben'];
				}
				return [];
			},
		}, 'rubric', this.common_fields);
		
		this.location = new LocationForm<true>({
			val_id: args.data?.location_id,
			required: true,
			selectable_options: args.aux.selectable_values.location,
			on_change: async (form) => {
				if (form instanceof LocationForm && form.get_value_in_editing()) {
					const selectable_places: IPlace[] = await form.get_all_places_in_value_in_editing();
					this.place.set_selectable_places(selectable_places);
				}
			},
			validate: async (value_in_editing): Promise<string[]> => {
				if (value_in_editing === undefined) {
					return ['Bitte einen auswählbaren Standort angeben'];
				}
				return [];
			},
		}, 'location', this.common_fields);
		
		this.place = new PlaceForm<true>({
			val_id: args.data?.place_id,
			required: true,
			selectable_options: args.aux.selectable_values.initial_places,
		}, 'place', this.common_fields);
		
		this.connected_exhibits = new ConnectedExhibitFrom<false>({
			val_ids: args.data?.connected_exhibit_ids,
			required: false,
			selectable_options: args.aux.selectable_values.initial_connected_exhibits,
		}, 'connected_exhibits', this.common_fields);
		
		// Bestandsdaten
		this.preservation_state = new SelectForm<IPreservationState, true>({
			val_id: args.data?.preservation_state_id,
			selectable_options: this.selectable_values.preservation_state,
			search_in: 'name',
			optionLabel: 'name',
			required: true,
			validate: async (value_in_editing) => {
				if (value_in_editing == undefined) {
					return ['Bitte einen auswählbaren Erhaltungszustand angeben'];
				}
				return [];
			},
		}, 'preservation_state', this.common_fields);
		
		const current_value = args.data?.current_value;
		this.current_value = new SingleValueForm2<number, number>({
			val: typeof current_value === 'number' ? this.form_price_amount(current_value) : undefined,
			required: false,
		}, 'current_value', this.common_fields);
		
		this.kind_of_property = new SelectForm<IKindOfProperty, true>({
			val_id: args.data?.kind_of_property_id,
			selectable_options: this.selectable_values.kind_of_property,
			search_in: 'name',
			optionLabel: 'name',
			required: true,
			validate: async (value_in_editing) => {
				if (value_in_editing === undefined) {
					return ['Bitte eine auswählbare Besitzart angeben'];
				}
				return [];
			},
		}, 'kind_of_property', this.common_fields);
		
		// Zugangsdaten
		const acquistion_date = args.data?.acquisition_info.date;
		const acquistion_purchasing_price = args.data?.acquisition_info.purchasing_price;
		this.acquisition_info = {
			date: new SingleValueForm2<Date, Date, true>({
				val: acquistion_date ? DateUtil.parse_iso_date(acquistion_date) : undefined,
				required: true,
				validate: (value_in_editing) => new Promise((resolve) => {
					if (value_in_editing) {
						resolve([]);
					} else {
						resolve(['Bitte ein Datum angeben']);
					}
				}),
			}, 'acquisition_date', this.common_fields),
			
			source: new StringForm<true>({
				val: args.data?.acquisition_info.source,
				required: true,
			}, 'source', this.common_fields),
			
			kind: new SelectForm<IKindOfAcquisition>({
				val_id: args.data?.acquisition_info.kind_id,
				required: false,
				selectable_options: this.selectable_values.kind_of_acquisition,
				optionLabel: 'name',
				search_in: 'name',
				validate: async (value_in_editing) => {
					if (value_in_editing === undefined) {
						return ['Bitte eine auswählbare Zugangsart angeben'];
					}
					return [];
				},
			}, 'kind_of_acquisition', this.common_fields),
			
			purchasing_price: new SingleValueForm2<number, number>({
				val: typeof acquistion_purchasing_price === 'number' ? this.form_price_amount(acquistion_purchasing_price) : undefined,
				required: false,
			}, 'purchasing_price', this.common_fields),
		};
		
		// Geräte- und Buchinformationen
		this.manufacturer = new StringForm<true>({
			val: args.data?.manufacturer,
			required: true,
		}, 'manufacturer', this.common_fields);
		
		this.manufacture_date = new PartialDateFrom({
			val: this.form_partial_date(args.data?.manufacture_date),
			required: false,
		}, 'manufacture_date', this.common_fields);
		
		const original_price_currency_not_validate = async () => [];
		const original_price_currency_validate = async (value_in_editing: ICurrency|null|undefined) => {
			if (value_in_editing) {
				if (this.selectable_values.currency.some((_selectable_value) => _selectable_value.id === value_in_editing.id)) {
					return [];
				} else {
					return ['Bitte eine auswählbare Währung angeben'];
				}
			} else {
				return ['Bitte eine Währung angeben'];
			}
		};
		
		const original_price_amount = args.data?.original_price.amount;
		const original_price_amount_initially_provided: boolean = typeof original_price_amount === 'number';
		this.original_price = {
			amount: new SingleValueForm2<number, number>({
				val: typeof original_price_amount === 'number' ? this.form_price_amount(original_price_amount) : original_price_amount,
				required: false,
				on_change: (form) => {
					const value_in_editing = form.get_value_in_editing();
					if (value_in_editing === null || value_in_editing == undefined) {
						this.original_price.currency.set_validate(original_price_currency_not_validate);
						this.original_price.currency.set_is_required(false);
						this.original_price.currency.set_is_required(false);
					} else {
						this.original_price.currency.set_validate(original_price_currency_validate);
						this.original_price.currency.set_is_required(true);
					}
				},
			}, 'original_price_amount', this.common_fields),
			
			currency: new SelectForm<ICurrency, boolean>({
				val_id: args.data?.original_price.currency_id,
				required: original_price_amount_initially_provided,
				selectable_options: this.selectable_values.currency,
				search_in: 'id',
				optionLabel: 'id',
				validate: original_price_amount_initially_provided ? original_price_currency_validate : original_price_currency_not_validate,
			}, 'original_price_currency', this.common_fields),
		};
		
		// Geräteinformationen
		const device_info = args.data?.device_info;
		this.device_info = {
			manufactured_from_date: new PartialDateFrom({
				val: this.form_partial_date(device_info?.manufactured_from_date),
				required: false,
			}, 'manufactured_from_date', this.device_fields),
			
			manufactured_to_date: new PartialDateFrom({
				val: this.form_partial_date(device_info?.manufactured_to_date),
				required: false,
			}, 'manufactured_to_date', this.device_fields),
		}
		
		// Buchinformationen
		const book_info = args.data?.book_info;
		this.book_info = {
			authors: new StringForm({
				val: book_info?.authors,
				required: false,
			}, 'authors', this.book_fields),
			
			language: new SelectForm<ILanguage, true>({
				val_id: book_info?.language_id,
				selectable_options: this.selectable_values.language,
				search_in: 'name',
				optionLabel: 'name',
				required: true,
				validate: async (value_in_editing) => {
					if (value_in_editing === undefined) {
						return ['Bitte eine auswählbare Sprache angeben'];
					}
					return [];
				},
			}, 'language', this.book_fields),
			
			isbn: new StringForm({
				val: book_info?.isbn,
				required: false,
			}, 'isbn', this.book_fields),
		};
		
		// Exponats-Typ
		const args_exhibit_type_id: string = (book_info === undefined) ? 'device' : 'book';
		const type: IExhibitType|undefined = args.aux.selectable_values.exhibit_type.find((v) => v.id === args_exhibit_type_id);
		if (!type) {
			throw new Error("exhibit type not found");
		}
		this.show_device_info = ref<boolean>(type.id === 'device');
		this.show_book_info = ref<boolean>(type.id === 'book');
		
		this.type = new SingleValueForm2<IExhibitType, IExhibitType, true>({
			val: type,
			required: true,
			on_change: (form: ISingleValueForm2<IExhibitType>) => {
				const type = form.get_value_in_editing();
				if (!type) {
					throw new Error("exhibit type not found");
				}
				this.show_device_info.value = type.id === 'device';
				this.show_book_info.value = type.id === 'book';
			},
		}, 'exhibit_type', this.common_fields);
	
	}
	
	private determinate_selectable_value_from_id<T extends { id: string }, R extends boolean = false>(id: string|undefined, selectable_values: T[], required?: R): R extends true ? T|undefined : T|null|undefined {
		if (id === undefined) {
			return undefined;
		}
		let value: T|null|undefined = selectable_values.find((selectable_value: T): boolean => selectable_value.id === id);
		if (required && (value === null || value === undefined)) {
			throw new Error(`ExhibitForm::determinate_selectable_value_from_id(): provided id ${id === undefined ? 'undefined' : "'"+id+"'"}, but no value could be found`);
		}
		if (value === undefined) {
			value = null;
		}
		// @ts-expect-error
		return value;
	};
	
	private success_toast(msg: string): void {
		this.toast_service.add({ severity: 'success', summary: msg, life: 3000 });
	}
	
	private failed_toast(msg: string): void {
		this.toast_service.add({ severity: 'error', summary: msg, life: 3000 });
	}
	
	private async on_child_field_change(form: IMultipleValueForm): Promise<void> {
		this.is_save_button_enabled.value = await this.is_valid();
	}
	
	private async is_valid(): Promise<boolean> {
		return (await Promise.all(this.determinate_relevant_multiple_value_forms()
			.map((multiple_form) => multiple_form.is_valid())
		)).every((result) => result);
	}
	
	private commit(): void {
		this.determinate_relevant_multiple_value_forms().forEach(multiple_form => multiple_form.commit());
	}
	
	private rollback(): void {
		this.get_all_multiple_value_forms().forEach(multiple_form => multiple_form.rollback());
	}
	
	private get_all_multiple_value_forms(): IMultipleValueForm[] {
		return [this.common_fields, this.device_fields, this.book_fields];
	}
	
	private determinate_relevant_multiple_value_forms(): IMultipleValueForm[] {
		const type = this.type.get_value_in_editing();
		if (!type || type.id === undefined || (type.id !== 'device' && type.id !== 'book')) {
			throw new Error("Assertation failed: no exhibit type");
		}
		const second = (type.id === 'device') ? this.device_fields : this.book_fields;
		return [this.common_fields, second];
	}
	
	private exists_in_db(): boolean {
		return this.id !== undefined;
	}
	
	public async click_delete(): Promise<void> {
		throw new Error("Method not implemented.");
	}
	
	public async click_save(): Promise<void> {
		this.commit();
		
		this.is_save_button_loading.value = true;
		if (this.exists_in_db()) {
			await this.ajax_update();
		} else {
			await this.ajax_create();
		}
		this.is_save_button_loading.value = false;
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
			method: "post",
			url: route('ajax.exhibit.create'),
			data: request_data,
		};
		
		return axios.request(request_config).then(
			(response: AxiosResponse<ExhibitAJAX.Create.I200ResponseData>) => {
				window.location.replace(route('exhibit.details', { exhibit_id: response.data }));
			},
			() => {
				this.failed_toast('neues Exponat konnte nicht gespeichert');
			}
		);
	}
	
	private create_or_update_request_data(): ExhibitAJAX.Create.IRequestData|ExhibitAJAX.Update.IRequestData {
		const original_price_amount = this.request_price_amount(this.original_price.amount.get_value());
		let original_price_currency_id = this.original_price.currency.get_value()?.id ?? '';
		if (original_price_amount !== null && !original_price_currency_id) {
			throw new Error(`Assertation failed: original_price.amount is given but not original_price.id`);
		}
		if (original_price_currency_id && original_price_amount === null) {
			original_price_currency_id = '';
		}
		
		const request_data: ExhibitAJAX.Create.IRequestData|ExhibitAJAX.Update.IRequestData = {
			inventory_number: this.inventory_number.get_value(),
			name: this.name.get_value(),
			short_description: this.short_description.get_value() ?? '',
			manufacturer: this.manufacturer.get_value(),
			manufacture_date: this.manufacture_date.get_value()?.format_iso() ?? '',
			preservation_state_id: this.preservation_state.get_value().id,
			original_price: {
				amount: original_price_amount,
				currency_id: original_price_currency_id,
			},
			current_value: this.request_price_amount(this.current_value.get_value()),
			acquisition_info: {
				date: DateUtil.format_as_iso_date(this.acquisition_info.date.get_value()),
				source: this.acquisition_info.source.get_value(),
				kind_id: this.acquisition_info.kind.get_value()?.id ?? '',
				purchasing_price: this.request_price_amount(this.acquisition_info.purchasing_price.get_value()),
			},
			kind_of_property_id: this.kind_of_property.get_value().id,
			place_id: this.place.get_value().id,
			rubric_id: this.rubric.get_value().id,
			//@ts-expect-error (TS-Bug)
			conntected_exhibit_ids: this.connected_exhibits.get_value().map((exhibit: IConnectedExhibit) => exhibit.id),
		};
		if (this.type.get_value().id === 'device') {
			request_data.device_info = {
				manufactured_from_date: this.device_info.manufactured_from_date.get_value()?.format_iso() ?? '',
				manufactured_to_date: this.device_info.manufactured_to_date.get_value()?.format_iso() ?? '',
			};
		}
		if (this.type.get_value().id === 'book') {
			request_data.book_info = {
				authors: this.book_info.authors.get_value() ?? '',
				isbn: this.book_info.isbn.get_value() ?? '',
				language_id: this.book_info.language.get_value().id,
			};
		}
		return request_data;
	}
	
	private form_partial_date(partial_date_iso: string|undefined): PartialDate.PartialDate|undefined {
		if (partial_date_iso === '' || partial_date_iso === undefined) {
			return undefined;
		}
		return PartialDate.PartialDate.parse_iso(partial_date_iso);
	}
	
	private form_price_amount(amount: number): number {
		return amount / 100;
	}
	
	private request_price_amount(amount: number|null): number|null {
		return (amount === null) ? null : Math.round(amount * 100);
	}
}
