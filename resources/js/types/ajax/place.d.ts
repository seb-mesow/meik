export namespace Query {
	export interface IQueryParams {
		location_id?: string,
		page_number?: number,
		count_per_page?: number,
	};
	export interface I200ResponseData {
		places: { id: string, name: string }[],
		total_count?: number,
	};
}

export namespace Create {
	export interface IRequestData {
		name: string, // Newer JSON spec does not allow a single string. The root must be an object.
	};
	export type I200ResponseData = string; // new ID
	export type I422ResponseData = string[]; // errs
}

export namespace Update {
	export interface IRequestData {
		name: string, // Newer JSON spec does not allow a single string. The root must be an object.
	};
	export type I200ResponseData = never;
	export type I422ResponseData = string[]; // errs
}

export namespace Delete {
	export type IRequestData = never;
	export type I200ResponseData = never;
	export type I422ResponseData = string[]; // errs
}
