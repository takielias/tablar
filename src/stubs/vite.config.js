import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Tabler's bundled bootstrap sass is noisy on Dart Sass 1.80+.
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
