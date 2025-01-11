export type IImagesIndicesOrder = number[];

export type IGetImageRequestData = never;
export type IGetImage200ResponseData = never; // binary
export type IGetImage422ResponseData = string[]; // errs

export interface ICreateImageQueryParams {
	index: number
};
export type ICreateImageRequestData = never; // binary
export interface ICreateFreeText200ResponseData {
	id: number,
	indices_order: IImagesIndicesOrder
}
export type ICreateFreeText422ResponseData = string[]; // errs

export interface ISetImageMetaDataRequestData {
	description: string[],
	is_public: boolean,
}
export type ISetImageMetaData200ResponseData = never;
export interface ISetImageMetaData422ResponseData {
	description: string[], // errs
	is_public: string[], // errs
}

export type IUpdateImageRequestData = never; // binary
export type IUpdateFreeText200ResponseData = never;
export type IUpdateFreeText422ResponseData = string[]; // errs

export type IDeleteImageRequestData = never;
export type IDeleteImage200ResponseData = IImageIndicesOrder;
export type IDeleteImage422ResponseData = string[]; // errs

export type IMoveimageRequestData = number; // new index
export type IMoveimage200ResponseData = IImagesIndicesOrder;
export type IMoveimage422ResponseData = string[]; // errs
