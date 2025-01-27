export interface IGetRubricsPaginatedQueryParams {
	page_number: number,
	count_per_page: number,
};
export interface IGetRubricsPaginated200ResponseData {
	rubrics: {
		id: string,
		name: string,
	}[],
	total_count: number,
};

export type ICreateRubricRequestData = {name: string, category: string}; // name

export type ICreateRubric200ResponseData = string; // new ID
export type ICreateRubric422ResponseData = string[]; // errs

export type IUpdateRubricRequestData = {id: string, name: string, category: string};; // new name
export type IUpdateRubric200ResponseData = never;
export type IUpdateRubric422ResponseData = string[]; // errs

export type IDeleteRubricRequestData = never;
export type IDeleteRubric200ResponseData = never;
export type IDeleteRubric422ResponseData = string[]; // errs
