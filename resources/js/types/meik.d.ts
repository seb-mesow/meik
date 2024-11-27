import { Config } from 'ziggy-js';

export interface Exhibit {
	name: string;
}

export interface FormValue<ID extends string, T> {
	id: ID,
	value: T,
	errors: string[],
}

export type FormValues<P extends Record<string, any> > = {
	[K in keyof P]: FormValue<P[K]>;
}
