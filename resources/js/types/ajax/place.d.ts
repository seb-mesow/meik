export interface IGetPlacesPaginatedQueryParams {
	page_number: number,
	count_per_page: number,
};
export interface IGetPlacesPaginated200ResponseData {
	places: {
		id: string,
		name: string,
	}[],
	total_count: number,
};

export type ICreatePlaceRequestData = string; // name

export type ICreatePlace200ResponseData = string; // new ID
export type ICreatePlace422ResponseData = string[]; // errs

export type IUpdatePlaceRequestData = string; // new name
export type IUpdatePlace200ResponseData = never;
export type IUpdatePlace422ResponseData = string[]; // errs

export type IDeletePlaceRequestData = never;
export type IDeletePlace200ResponseData = never;
export type IDeletePlace422ResponseData = string[]; // errs
