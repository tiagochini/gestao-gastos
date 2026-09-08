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
            registerType: 'autoUpdate',
            outDir: 'public',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            scope: '/',
            buildBase: '/',
            includeAssets: ['favicon.ico', 'pwa/icon.svg'],
            manifest: false,
            workbox: {
                navigateFallback: '/offline.html',
                globPatterns: [
                    'build/**/*.{js,css,html,png,svg,woff,woff2}',
                    'pwa/*.{png,svg}',
                    'manifest.webmanifest',
                    'offline.html',
                    'favicon.ico',
                ],
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) => request.destination === 'style' || request.destination === 'script',
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'static-assets',
                        },
                    },
                    {
                        urlPattern: ({ request }) => request.destination === 'image' || request.destination === 'font',
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'media-assets',
                            expiration: {
                                maxEntries: 80,
                                maxAgeSeconds: 60 * 60 * 24 * 30,
                            },
                        },
                    },
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
