import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            // PENTING UNTUK LARAVEL: Mengarahkan build ke folder public luar agar service worker terdaftar dengan benar di root domain
            buildBase: '/',
            scope: '/',
            manifest: {
                name: 'Kos Putri Sunduwan',
                short_name: 'Sunduwan',
                description: 'Kos Putri Sunduwan',
                theme_color: '#f43f5e',
                background_color: '#ffffff',
                display: 'standalone',
                scope: '/',
                start_url: '/',
                icons: [
                    {
                        src: '/images/logo-sunduwan-pwa.png', // Pastikan file ini ada di public/images/logo-sunduwan-pwa.png
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any'
                    },
                    {
                        src: '/images/logo-sunduwan-pwa.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable' // Sangat baik sudah menyediakan varian maskable!
                    }
                ]
            },
            // Tambahan konfigurasi jika ingin mengontrol aset apa saja yang masuk ke cache offline workbox
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff,woff2}'],
                // Menghindari konflik manifest bawaan Laravel Mix/Vite
                dontCacheBustURLsMatching: /\.[0-9a-f]{8}\./,
            }
        })
    ],
});