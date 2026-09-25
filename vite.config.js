import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify'
import { VitePWA } from 'vite-plugin-pwa'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        vuetify({ autoImport: true }),
        VitePWA({
            strategies: 'injectManifest',
            registerType: 'autoUpdate',
            srcDir: 'resources/js',
            outDir: 'public',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            scope: '/',
            buildBase: '/',
            includeAssets: ['favicon.ico', 'pwa/icon.svg'],
            manifest: false,
            injectManifest: {
                globPatterns: [
                    'build/**/*.{js,css,html,png,svg,woff,woff2}',
                    'pwa/*.{png,svg}',
                    'manifest.webmanifest',
                    'offline.html',
                    'favicon.ico',
                ],
            },
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
});
