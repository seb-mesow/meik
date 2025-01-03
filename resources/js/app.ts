import '../css/app.css';
import 'primeicons/primeicons.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, createSSRApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import Lara from '@primevue/themes/lara';
import ToastService from 'primevue/toastservice';
// import Material from '@primevue/themes/lara';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // für lokale Dev
        const app = createApp({ render: () => h(App, props) });
        // für Produktiv
        // const app = createSSRApp({ render: () => h(App, props) });
        
		app.use(plugin);
		
		// Plugins für Vue
        app.use(ZiggyVue);
		app.use(PrimeVue, {
			theme: {
				preset: Lara, // oder Material
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
