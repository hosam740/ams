import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Global
                'resources/css/base.css',

                // App (dashboard)
                'resources/css/app.css',
                'resources/js/app.js',

                // Auth
                'resources/css/auth.css',
                'resources/js/auth.js',

                // Bootstrap
                'resources/js/bootstrap.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
