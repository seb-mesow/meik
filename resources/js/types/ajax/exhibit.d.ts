import { IExhibitTileProps } from "../page_props/exhibit_overview";

namespace GetTilesPaginated {
	export interface IQueryParams {
		page_number: number,
		rubric_id?: string,
	};
	export type IRequestDate = never;
	export type I200ResponseData = IExhibitTileProps[]; // vielleicht ist irgendwann mal total_count nötig
	export type I422ResponseData = never; // undefined interface
}

namespace Create {
	export interface IRequestData {
		inventory_number: string,
		name: string,
		short_description: string,
		manufacturer: string,
		manufacture_date: string,
		preservation_state_id: string,
		original_price: {
			amount: number,
			currency_id: string,
		},
		current_value: number,
		acquisition_info: {
			date: string,
			source: string,
			kind_id: string,
			purchasing_price: number,
		},
		kind_of_property_id: string,
		device_info?: {
			manufactured_from_date: string,
			manufactured_to_date: string,
		},
		book_info?: {
			authors: string,
			isbn: string,
			language_id: string,
		},
		place_id: string,
		rubric_id: string,
		conntected_exhibit_ids: number[],
	};
	export type I200ResponseData = number; // new exhibit ID
	export type I422ResponseData = never;
}

namespace Update {
	export type IRequestData = Create.IRequestData;
	export type I200ResponseData = never;
	export type I422ResponseData = never;
}
