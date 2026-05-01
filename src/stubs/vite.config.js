import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

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
                    dest: '../dist',
                },
                {
                    src: 'node_modules/@tabler/icons-webfont/dist/fonts',
                    dest: '../build/assets',
                },
            ],
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Tabler core's sass triggers Dart Sass 1.80+ deprecations
                // (@import, legacy color funcs, /-as-division). Silence the
                // third-party noise; the package source is clean.
                quietDeps: true,
                silenceDeprecations: [
                    'import',
                    'global-builtin',
                    'color-functions',
                    'legacy-js-api',
                    'slash-div',
                    'if-function',
                ],
            },
        },
    },
});
