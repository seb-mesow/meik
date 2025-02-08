import { createApp } from "vue";
import { IExhibitTileProps } from "../page_props/exhibit_overview";

namespace GetPaginated {
	export interface IQueryParams {
		rubric_id?: string,
		page_number?: number,
		count_per_page?: number,
	};
	export type I200ResponseData = IExhibitTileProps[]; // vielleicht ist irgendwann mal total_count nötig
	export type I422ResponseData = never; // undefined interface
}

namespace Update {
	export interface IRequestData {
		inventory_number: string,
		name: string,
		short_description: string,
		manufacturer: string,
	};
	export type I200ResponseData = never;
	export type I422ResponseData = never;
}

namespace Create {
	export type IRequestData = Update.IRequestData;
	export type I200ResponseData = number; // new exhibit ID
	export type I422ResponseData = never;
}
