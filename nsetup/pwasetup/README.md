# PWA Setup for Laravel + Vite

This folder contains a script to help you quickly set up Progressive Web App (PWA) support for your Laravel application using `vite-plugin-pwa`.

## What's Included

1.  **scripts/setup-pwa.js**: A Node.js script that automates the configuration process.

---

## How to Use

### 1. Run the Setup Script

You can run the script directly using `node`:

```bash
node Nsetup/pwasetup/scripts/setup-pwa.js
```

This script will:
1.  Check if `vite-plugin-pwa` is installed.
2.  Add PWA configuration to your `vite.config.js` if missing.
3.  Create a service worker registration script at `resources/js/pwa.js`.
4.  Import the registration script in your `resources/js/app.js`.

### 2. Manual Verification

After running the script, ensure the following:

**1. Icons:**
Make sure you have your PWA icons in the `public/` directory. The configuration expects:
- `icon.svg` (or favicon)
- `android-chrome-192x192.png`
- `android-chrome-512x512.png`
- `apple-touch-icon.png`

**2. Blade Layout:**
Add the manifest link and theme color to your main layout (e.g., `resources/views/layouts/app.blade.php` or `guest.blade.php`) inside the `<head>` tag:

```html
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#ffffff">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
```

### 3. Build

Run the build command to generate the service worker and manifest:

```bash
npm run build
```

The service worker file `sw.js` and `manifest.webmanifest` should be generated in `public/build/` or `public/` depending on your configuration.

---

## Troubleshooting

-   **Service Worker not registering?**
    Check the browser console for errors. Ensure you are serving over HTTPS (or localhost).
-   **Manifest not found?**
    Verify `vite.config.js` `outDir`. If using `laravel-vite-plugin`, assets often go to `public/build`, but PWA files might need to be in `public`. You may need to manually move them or configure `vite-plugin-pwa` to output to `public`.
