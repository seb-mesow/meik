export interface IGetLocationsPaginatedQueryParams {
	page_number: number,
	count_per_page: number,
};
export interface IGetLocationsPaginated200ResponseData {
	locations: {
		id: string,
		name: string,
		is_public: boolean,
	}[],
	total_count: number,
};

export interface ICreateLocationRequestData {
	val: {
		name: {
			val: string
		},
		is_public: {
			val: boolean
		}
	}
};
export type ICreateLocation200ResponseData = string; // new ID
export interface ICreateLocation422ResponseData {
	errs: string[],
	val: {
		name: {
			errs: string[]
		},
		is_public: {
			errs: string[]
		}
	}
};

export interface IUpdateLocationRequestData {
	val: {
		name: {
			val: string
		},
		is_public: {
			val: boolean
		}
	}
};
export type IUpdateLocation200ResponseData = never;
export interface IUpdateLocation422ResponseData {
	errs: string[],
	val: {
		name: {
			errs: string[]
		},
		is_public: {
			errs: string[]
		}
	}
};

export type IDeleteLocationRequestData = never;
export type IDeleteLocation200ResponseData = never;
export type IDeleteLocation422ResponseData = string[]; // errs
