import { IConnectedExhibit, ICurrency, IKindOfAcquisition, IKindOfProperty, ILanguage, IPreservationState } from "@/form/special/multiple/exhibit-form";
import { IFreeTextsPageProps } from "./freetexts"
import { IPlace } from "@/form/single/special/place-form";
import { ICategoryWithRubrics } from "@/form/special/single/rubric-form";
import { ILocation } from "@/form/special/single/location-form";

export interface ISelectableValuesProps {
	categories_with_rubrics: ICategoryWithRubrics[],
	location: ILocation[],
	initial_places?: IPlace[],
	preservation_state: IPreservationState[],
	kind_of_property: IKindOfProperty[],
	kind_of_acquisition: IKindOfAcquisition[],
	currency: ICurrency[],
	language: ILanguage[],
	initial_connected_exhibits: IConnectedExhibit[],
}

export interface IExhibitProps {
	id: number,
	
	// Kerndaten
	inventory_number: string,
	name: string,
	short_description: string,
	// rubric_id muss nicht, da schon als anderweitige Page-Prop
	location_id: string,
	place_id: string,
	// TODO connected_exhibits
	connected_exhibit_ids: int[],
	// Bestandsdaten
	preservation_state_id: string,
	current_value: number,
	kind_of_property_id: string,
	
	// Zugangsdaten
	acquisition_info: {
		date: string,
		source: string,
		kind_id: string,
		purchasing_price: number,
	},
	
	// Geräte- und Buchinformationen
	manufacturer: string,
	manufacture_date: string,
	original_price: {
		amount: number,
		currency_id: string,
	},
	
	// Geräteinformationen
	device_info?: {
		manufactured_from_date: string,
		manufactured_to_date: string,
	},
	
	// Buchinformationen
	book_info?: {
		authors: string,
		language_id: string,
		isbn: string,
	},
	
	title_image?: {
		id: string,
		description: string,
		image_width: number,
		image_height: number,
	},
	
	free_texts: IFreeTextsPageProps[],
}
