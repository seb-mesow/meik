export type IImageIDsOrder = string[];

namespace Create {
	export interface IRequestData {
		index: number,
		description: string,
		is_public: boolean,
		image: File,
	}
	export interface I200ResponseData {
		id: string,
		ids_order: IImageIDsOrder, // current order of IDs of images already in the database
	};
	export interface I422ResponseData {
		errs: string[],
		index: string[], // errs
		description: string[], // errs
		is_public: string[], // errs
		image: string[], // errs
	};
}

namespace UpdateMetaData {
	export interface IRequestData {
		description: string,
		is_public: boolean,
	}
	export type I200ResponseData = never;
	export interface I422ResponseData {
		errs: string[],
		description: string[], // errs
		is_public: string[], // errs
	}
}

namespace Replace {
	export interface IRequestData {
		description: string,
		is_public: boolean,
		image: File,
	}
	export type I200ResponseData = string; // new Image-ID
	export interface I422ResponseData {
		errs: string[],
		description: string[], // errs
		is_public: string[], // errs
		image: string[], // errs
	}
}

namespace Delete {
	export type IRequestData = never;
	export type I200ResponseData = IImageIndicesOrder; // current order of IDs of images already in the database
	export type I422ResponseData = string[]; // errs
}

namespace Move {
	export type IRequestData = IImageIDsOrder; // new order of IDs of images already in the database
	export type I200ResponseData = never;
	export type I422ResponseData = string[]; // errs
}
