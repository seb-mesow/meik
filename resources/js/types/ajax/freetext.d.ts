export type IFreeTextIndicesOrder = number[];

export interface ICreateFreeTextRequestData {
	index: number,
	val: {
		heading: {
			val: string
		},
		html: {
			val: string
		}
		is_public: {
			val: boolean
		}
	}
};
export interface ICreateFreeText200ResponseData {
	id: number,
	indices_order: IFreeTextIndicesOrder
}
export interface ICreateFreeText422ResponseData {
	errs: string[];
	val: {
		heading: {
			errs: string[],
		},
		html: {
			errs: string[],
		},
		is_public: {
			errs: string[],
		}
	}
};

export interface IUpdateFreeTextRequestData {
	val: {
		heading: {
			val: string,
		},
		html: {
			val: string,
		},
		is_public: {
			val: boolean,
		}
	}
};
export type IUpdateFreeText200ResponseData = never
export interface IUpdateFreeText422ResponseData {
	errs: string[],
	val: {
		heading: {
			errs: string[],
		},
		html: {
			errs: string[],
		},
		is_public: {
			errs: string[],
		}
	}
}; 

export type IDeleteFreeTextRequestData = never
export type IDeleteFreeText200ResponseData = IFreeTextIndicesOrder
export type IDeleteFreeText422ResponseData = never

export type IMoveFreeTextRequestData = number; // new index
export type IMoveFreeText200ResponseData = IFreeTextIndicesOrder
export type IMoveFreeText422ResponseData = never
