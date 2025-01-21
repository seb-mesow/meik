import '../css/app.css';
import 'primeicons/primeicons.css';
import '@/bootstrap-in-browser';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
//@ts-ignore
import { Ziggy } from './ziggy/ziggy';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import Lara from '@primevue/themes/lara';
import ToastService from 'primevue/toastservice';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
	// !!!!!
	// Änderungen auch in ssr.js übernehmen
	// !!!!!
	title(title) {
		return `${title} - ${appName}`;
	},
	resolve(name) {
		return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue'));
	},
	setup({ el, App, props, plugin }) {
		// für lokale Dev
		const app = createApp({ render: () => h(App, props) });
		// für Produktiv
		// const app = createSSRApp({ render: () => h(App, props) });
		
		app.use(plugin);
		
		// Plugins für Vue
		app.use(ZiggyVue, Ziggy);
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
		
		app.mount(el);
	},
	progress: {
		color: '#4B5563',
	},
});
