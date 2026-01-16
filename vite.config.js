import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',

                // ✅ AGREGAR ESTOS JS
                'resources/js/asistentes.js',
            
                'resources/js/dash.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: [
                'resources/css/app.css',
                'resources/js/app.js',

                // ✅ LOS MISMOS AQUÍ
                'resources/js/asistentes.js',
               
                'resources/js/dash.js',
            ],
        },
    },
});
