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

export interface IUpdateImageMetaDataRequestData {
	description: string,
	is_public: boolean,
}
export type IUpdateImageMetaData200ResponseData = never;
export interface IUpdateImageMetaData422ResponseData {
	errs: string[],
	description: string[], // errs
	is_public: string[], // errs
}

export interface IReplaceImageRequestData {
	description: string,
	is_public: boolean,
	image: File,
}
export type IReplaceImage200ResponseData = string; // new Image-ID
export interface IReplaceImage422ResponseData {
	errs: string[],
	description: string[], // errs
	is_public: string[], // errs
	image: string[], // errs
}

export type IDeleteImageRequestData = never;
export type IDeleteImage200ResponseData = IImageIndicesOrder;
export type IDeleteImage422ResponseData = string[]; // errs

// TODO does this still work?
export type IMoveImageRequestData = number; // new index
export type IMoveImage200ResponseData = IImageIDsOrder;
export type IMoveImage422ResponseData = string[]; // errs
