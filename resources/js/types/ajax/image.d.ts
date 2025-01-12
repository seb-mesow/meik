export type IImagesIndicesOrder = number[];

export type ICreateImageRequestData = {
	index: number,
	description: string,
	is_public: boolean,
};
export interface ICreateImage200ResponseData {
	id: number,
	indices_order: IImagesIndicesOrder,
};
export interface ICreateImage422ResponseData {
	index: string[], // errs
	description: string[], // errs
	is_public: string[], // errs
};

export type IDeleteImageRequestData = never;
export type IDeleteImage200ResponseData = IImageIndicesOrder;
export type IDeleteImage422ResponseData = string[]; // errs

export type IMoveImageRequestData = number; // new index
export type IMoveImage200ResponseData = IImagesIndicesOrder;
export type IMoveImage422ResponseData = string[]; // errs

export type IGetImageMetaDataRequestData = never;
export interface IGetImageMetaData200ResponseData {
	description: string,
	is_public: string,
};
export type IGetImageMetaData422ResponseData = string[]; // errs

export interface IUpdateImageMetaDataRequestData {
	description: string,
	is_public: boolean,
}
export type IUpdateImageMetaData200ResponseData = never;
export interface IUpdateImageMetaData422ResponseData {
	description: string[], // errs
	is_public: string[], // errs
}

export type ISetImageFile200ResponseData = never;
export type ISetImageFile422ResponseData = string[];
