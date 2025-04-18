import '../css/app.css';
import 'primeicons/primeicons.css';

import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, DefineComponent, h } from 'vue';
import { route as ziggy_route } from 'ziggy-js';
//@ts-ignore
import { Ziggy as _Ziggy } from './ziggy/ziggy';

// Prime Vue
import PrimeVue from 'primevue/config';
import Lara from '@primevue/themes/lara';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import DialogService from 'primevue/dialogservice';
import Tooltip from 'primevue/tooltip';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

(globalThis as any).Ziggy = _Ziggy;

createServer(page => createInertiaApp({
	page: page,
	render: renderToString,
	title(title) {
		return `${title} - ${appName}`;
	},
	resolve(name) {
		return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue'));
	},
	setup({ App, props, plugin }) {
		// für lokale Dev
		const app = createSSRApp({ render: () => h(App, props) });
		
		app.use(plugin);
		
		app.mixin({
			methods: {
				// route: (name: string, params: keyof RouteList , absolute: boolean, config = _Ziggy) => route(name, params, absolute, config),
				route: (name: any, parms: any, absolute: any, config: any) => {
					return ziggy_route(name, parms, absolute, config);
				},
			},
		});
		
		// PrimeVue
		app.use(PrimeVue, {
			theme: {
				preset: Lara, // oder Material
				options: {
					darkModeSelector: '.p-dark'
				}
			}
		});
		app.use(ConfirmationService);
		app.use(ToastService);
		app.use(DialogService);
		app.directive('tooltip', Tooltip);
		
		// ohne app.mount(el);
		// stattdessen:
		return app;
	},
	progress: {
		color: '#4B5563',
	},
}));
