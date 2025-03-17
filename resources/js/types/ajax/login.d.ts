export namespace Login {
	export interface IQueryParams {
		rubric_id?: string,
		page_number?: number,
		count_per_page?: number,
	};
	export interface IRequestDate {
		username: string,
		password: string,
		remember: boolean,
	};
	export type I200ResponseData = IExhibitTileProps[]; // vielleicht ist irgendwann mal total_count nötig
	export type I422ResponseData = never; // undefined interface
}
