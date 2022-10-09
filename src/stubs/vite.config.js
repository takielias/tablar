import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            port: 3000
        }
    },
    build: {
        commonjsOptions: {
            transformMixedEsModules: true
        }
    }
});
