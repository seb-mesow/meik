import '../css/app.css';
import 'primeicons/primeicons.css';
import '@/bootstrap-in-browser';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
//@ts-ignore
import { Ziggy } from './ziggy/ziggy';

// Prime Vue
import PrimeVue from 'primevue/config';
import Lara from '@primevue/themes/lara';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import DialogService from 'primevue/dialogservice';
import Tooltip from 'primevue/tooltip';

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
		
		app.use(plugin);
		
		// Plugins für Vue
		app.use(ZiggyVue, Ziggy);
		
		// PrimeVue
		app.use(PrimeVue, {
			theme: {
				preset: Lara, // oder Material
				options: {
					darkModeSelector: '.p-dark'
				}
			},
			locale: {
				dateFormat: 'dd.mm.yy',
				today: "Heute",
				weekHeader: "KW",
				firstDayOfWeek: 1,
				dayNames: ["Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
				dayNamesMin: ["So", "Mo", "Di", "Mi", "Do", "Fr", "Sa"],
				dayNamesShort: ["Son", "Mon", "Die", "Mit", "Don", "Fre", "Sam"],
				monthNames: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
				monthNamesShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
			}
		});
		app.use(ConfirmationService);
		app.use(ToastService);
		app.use(DialogService);
		app.directive('tooltip', Tooltip);
		
		app.mount(el);
	},
	progress: {
		color: '#4B5563',
	},
});
