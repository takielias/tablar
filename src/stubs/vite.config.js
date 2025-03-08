import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import {viteStaticCopy} from 'vite-plugin-static-copy'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/@tabler/core/dist/img',
                    dest: '../dist'
                },
                {
                    src: 'node_modules/@tabler/icons-webfont/dist/fonts',
                    dest: '../build/assets'
                }
            ]
        })
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
