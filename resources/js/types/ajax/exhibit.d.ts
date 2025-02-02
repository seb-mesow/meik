import { IExhibitTileProps } from "../page_props/exhibit_overview";

export interface IGetExhibitsPaginatedQueryParams {
	rubric_id?: string,
	page_number?: number,
	count_per_page?: number,
};
export type IGetExhibitsPaginated200ResponseData = IExhibitTileProps[]; // vielleicht ist irgendwann mal total_count nötig
export type IGetExhibitsPaginated422ResponseData = never; // undefined interface
