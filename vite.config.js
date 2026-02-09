import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/dashboard.css",
                "resources/js/app.js",
                "resources/js/dash.js",
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: "manifest.json", // 👈 CLAVE
        outDir: "public/build",
        emptyOutDir: true,
    },
});
