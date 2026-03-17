import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
// import { VitePWA } from 'vite-plugin-pwa'; // Uncomment if using PWA
// import tailwindcss from '@tailwindcss/vite'; // Uncomment if using TailwindCSS

export default defineConfig(({ mode }) => {
    // Load env file based on `mode` in the current working directory.
    // Set the third parameter to '' to load all env regardless of the `VITE_` prefix.
    const env = loadEnv(mode, process.cwd(), '');
    const ngrokUrl = env.VITE_NGROK_URL || '';

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            // tailwindcss(), // Uncomment if using TailwindCSS
            // VitePWA({...}), // Uncomment/Configure if using PWA (or run nsetup PWA Setup again)
        ],
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
            // Allow ngrok to reach the Vite dev server
            allowedHosts: ['all'],
            ...(ngrokUrl ? {
                host: '0.0.0.0',
                hmr: {
                    host: ngrokUrl.replace('https://', '').replace('http://', ''),
                    protocol: 'wss',
                },
            } : {
                host: '127.0.0.1',
            }),
        },
    };
});
