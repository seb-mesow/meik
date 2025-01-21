import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import { route as ziggyRoute } from 'ziggy-js';
import { PageProps as AppPageProps } from './';

declare global {
	interface Window {
		axios: AxiosInstance;
	}

	/* eslint-disable no-var */
	// var route: typeof ziggyRoute;
}

// declare module 'vue' {
	// interface ComponentCustomProperties {
		// route: typeof ziggyRoute;
	// }
// }

// declare module 'ziggy-js' {
// 	function route(name: RouteName, params: RouteParams, absolute): void
// }

declare module '@inertiajs/core' {
	interface PageProps extends InertiaPageProps, AppPageProps {}
}
