import { IExhibitTileProps } from "../page_props/exhibit_overview";

namespace Query {
	export interface IQueryParams {
		category_id?: string,
		page_number?: number,
		count_per_page?: number,
	};
	export type IRequestData = never;
	export type I200ResponseData = {
		rubrics: { id: string, name: string }[],
		total_count?: number,
	};
}

namespace Create {
	export interface IRequestData {
		name: string, 
		category_id: string
	};
	export type I200ResponseData = string; // new ID
	export type I422ResponseData = string[]; // errs
}

namespace Update {
	export interface IRequestData {
		// id: string,
		name: string, 
		category_id: string
	};
	export type I200ResponseData = never;
	export type I422ResponseData = string[]; // errs
}

namespace Delete {
	export type IRequestData = never;
	export type I200ResponseData = never;
	export type I422ResponseData = string[]; // errs
}
