export namespace Query {
	export interface IQueryParams {
		page_number?: number,
		count_per_page?: number,
	};
	export interface I200ResponseData {
		users: {
			id: string,
			username: string,
			forename: string,
			surname: string,
			role_id: string,
		}[],
		total_count: number, // Größe der Ergebnismenge unter Berücksichtigung der Suchkriterien
	};
}

export namespace Create {
	export interface IRequestData {
		username: string,
		forename: string,
		surname: string,
		password: string,
		role_id: string,
	};
	export type I200ResponseData = string; // new User ID
	export interface I422ResponseData {};
}

export namespace Update {
	export interface IRequestData {
		username: string,
		forename: string,
		surname: string,
		role_id: string,
	};
	export interface I200ResponseData {};
	export interface I422ResponseData {};
}

export namespace Delete {
	export interface IRequestData {};
	export interface I200ResponseData {};
	export interface I422ResponseData {};
}

export namespace SetPassword {
	export interface IRequestData {
		password: string,
	};
	export interface I200ResponseData {};
	export interface I422ResponseData {};
}
