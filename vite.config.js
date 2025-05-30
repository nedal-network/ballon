import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/filament.css', 'resources/css/checking.css', 'resources/css/list-checkins.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
