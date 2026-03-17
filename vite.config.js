import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => {
    // Load env file based on `mode` in the current working directory.
    // Set the third parameter to '' to load all env regardless of the `VITE_` prefix.
    const env = loadEnv(mode, process.cwd(), '');


    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
            VitePWA({
                outDir: 'public',
                registerType: 'autoUpdate',
                injectRegister: 'auto',
                workbox: {
                    navigateFallback: null,
                    globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
                    globIgnores: ['**/manifest.webmanifest'],
                    runtimeCaching: [
                        {
                            // Cache HTML pages (Dashboard, Logbook)
                            urlPattern: ({ request }) => request.mode === 'navigate',
                            handler: 'NetworkFirst',
                            options: {
                                cacheName: 'pages-cache',
                                expiration: {
                                    maxEntries: 50,
                                    maxAgeSeconds: 60 * 60 * 24 * 7, // 7 Days
                                },
                            },
                        },
                        {
                            // Cache API requests (if needed in future, though mostly handled by local storage logic)
                            urlPattern: ({ url }) => url.pathname.startsWith('/api'),
                            handler: 'NetworkFirst',
                            options: {
                                cacheName: 'api-cache',
                                expiration: {
                                    maxEntries: 100,
                                    maxAgeSeconds: 60 * 60 * 24, // 1 Day
                                },
                            },
                        },
                    ],
                },
                manifest: {
                    name: env.VITE_APP_NAME || 'HinaTourist',
                    short_name: 'HinaTourist',
                    description: 'Your premium tourist application experience.',
                    theme_color: '#6366f1',
                    start_url: '/',
                    scope: '/',
                    display: 'standalone',
                    icons: [
                        {
                            src: '/hinatourist-logo.png',
                            sizes: 'any',
                            type: 'image/png',
                            purpose: 'any'
                        }
                    ]
                }
            }),
        ],
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
            host: '127.0.0.1',
        },
    };
});
