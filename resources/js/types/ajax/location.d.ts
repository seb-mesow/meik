export namespace Query {
	export interface IQueryParams {
		page_number?: number,
		count_per_page?: number,
	};
	export interface I200ResponseData {
		locations: {
			id: string,
			name: string,
			is_public: boolean,
		}[],
		total_count: number, // Größe der Ergebnismenge unter Berücksichtigung der Suchkriterien
	};
}

export namespace Create {
	export interface IRequestData {
		name: string,
		is_public: boolean,
	};
	export type I200ResponseData = string; // new ID
	export interface I422ResponseData {
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
}

export namespace Update {
	export interface IRequestData {
		name: string,
		is_public: boolean,
	};
	export type IResponseData = never;
	export interface I422ResponseData {
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
}

export namespace Delete {
	export type IRequestData = never;
	export type I200ResponseData = never;
	export type I422ResponseData = string[]; // errs
}
