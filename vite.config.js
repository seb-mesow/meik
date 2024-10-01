import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.ts',
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
	build: {
        watch: { // https://rollupjs.org/configuration-options/#watch
            include: [
                '(^|/)storage/framework/views/.*', // normal regex
                '(^|/)bootstrap/ssr/.*',
                '(^|/)resources/.*',
                '(^|/)public/.*',
                '(^|/)node_modules/.*',
            ],
        }
    },
	server: {
        // see
        // https://github.com/vitejs/vite/discussions/6473#discussioncomment-4461746
        // https: {
        //     // https://nodejs.org/api/https.html#https_https_createserver_options_requestlistener
        //     cert: '/etc/nginx/certificates/leftoverchef.local.crt',
        //     key: '/etc/nginx/certificates/leftoverchef.local.key',
        // },
        host: 'node', // except connections from outside
        // port: 5173,  // default port is 5173
        strictPort: true, // ensure port is really 5173
        hmr: {
        //     // port: 5174, // within the virtual machine serve websocket connections via port 5174
        //     // protocol: "wss", 
            host: 'meik.localhost',
            clientPort: 8080,
        //     // host: 'localhost',
        //     // if accessing the Webapp FROM INSIDE THE VIRTUAL MACHINE (connect via port 80)
        //     // clientPort: 443, // only for the links to assets and scripts in documents delivered to the browser
        //     // if accessing the Web-App FROM THE HOST (connect via Port 81)
        //     clientPort: 444, // only for the links to assets and scripts in documents delivered to the browser
            path: '/websocket', // special path for requests to start a websocket connection
        },
	}
});
