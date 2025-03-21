import { Config } from 'ziggy-js';
import { Permissions } from './permissions';

export type NotUndefined<T> = T extends undefined ? never : T;

export interface User {
	id: number;
	name: string;
	email: string;
	email_verified_at?: string;
}

export type PageProps<
	T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
	auth: {
		user: User;
		permissions: Permissions;
	};
	ziggy: Config & { location: string };
};
