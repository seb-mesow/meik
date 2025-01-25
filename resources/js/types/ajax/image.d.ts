export type IImageIDsOrder = string[];

export interface ICreateImageRequestData {
	index: number,
	description: string,
	is_public: boolean,
	image: File,
}
export interface ICreateImage200ResponseData {
	id: string,
	ids_order: IImageIDsOrder,
};
export interface ICreateImage422ResponseData {
	errs: string[],
	index: string[], // errs
	description: string[], // errs
	is_public: string[], // errs
	image: string[], // errs
};

export interface IUpdateImageRequestData {
	description: string,
	is_public: boolean,
	image?: File,
}
export type IUpdateImage200ResponseData = never;
export interface IUpdateImage422ResponseData {
	errs: string[],
	description: string[], // errs
	is_public: string[], // errs
	image?: string[], // errs
}

export type IDeleteImageRequestData = never;
export type IDeleteImage200ResponseData = IImageIndicesOrder;
export type IDeleteImage422ResponseData = string[]; // errs

export type IMoveImageRequestData = number; // new index
export type IMoveImage200ResponseData = IImageIDsOrder;
export type IMoveImage422ResponseData = string[]; // errs
