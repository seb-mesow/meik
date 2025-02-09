import { IExhibitTileProps } from "../page_props/exhibit_overview";

namespace GetPaginated {
	export interface IQueryParams {
		page_number: number,
		category_id?: string,
	};
	export type IRequestData = never;
	export type I200ResponseData = {
		id: string,
		name: string,
	}[];
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
