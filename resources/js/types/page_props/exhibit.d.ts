import { ICurrency, IKindOfAcquistion, IKindOfProperty, ILanguage, ILocation, IPreservationState } from "@/form/exhibitform";
import { IFreeTextsPageProps } from "./freetexts"

export interface ISelectableValuesProps {
	currency: ICurrency[],
	kind_of_acquistion: IKindOfAcquistion[],
	kind_of_property: IKindOfProperty[],
	language: ILanguage[],
	preservation_state: IPreservationState[],
	location: ILocation[],
}

export interface IExhibitProps {
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
		date: string,
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
