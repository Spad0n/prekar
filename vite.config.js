import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";

/* if you're using React */
// import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        /* react(), // if you're using React */
        symfonyPlugin(),
    ],
    publicDir: "assets/static",
    build: {
        rollupOptions: {
            input: {
                htmx: "./assets/htmx-global.js",
		alpinejs: "./assets/alpine-global.js",
                styles: "./assets/styles/app.css",
            },
        }
    },
});
