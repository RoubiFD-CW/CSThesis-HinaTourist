# Ngrok Setup for Laravel + Vite

This folder contains reusable code to enable ngrok support for Laravel applications with Vite.

## What's Included

1. **NgrokServiceProvider.php** - Laravel service provider for dynamic ngrok detection
2. **bootstrap-app-snippet.php** - Code to add to your `bootstrap/app.php`
3. **vite.config.js** - Complete Vite configuration with ngrok support
4. **env-snippet.txt** - Code to add to your `.env` file

## Installation Steps (Automatic)

Run the automated setup script to configure ngrok, update environment variables, and patch your files:

```bash
npm run setup:ngrok
```

Or run manually with node:

```bash
node Nsetup/ngroksetup/scripts/setup-ngrok.js
```

This script will:
- Copy the Service Provider
- Register it in `bootstrap/providers.php`
- Configure `bootstrap/app.php`
- Check `vite.config.js`
- Set up your `.env` file

---

## Installation Steps (Manual)

### Step 1: Copy the Service Provider

Copy `NgrokServiceProvider.php` to your project:
```
app/Providers/NgrokServiceProvider.php
```

### Step 2: Register the Service Provider

Add this line to `bootstrap/providers.php`:
```php
App\Providers\NgrokServiceProvider::class,
```

### Step 3: Trust Proxies

In `bootstrap/app.php`, modify the `withMiddleware` section:
```php
->withMiddleware(function (Middleware $middleware): void {
    // Trust all proxies (for ngrok and other tunneling services)
    $middleware->trustProxies(at: '*');
})
```

You also need to add the import at the top:
```php
use Illuminate\Http\Request;
```

### Step 4: Update Vite Config

Replace your `vite.config.js` with the one provided, or merge the configuration.

### Step 5: Add Environment Variables

Add to your `.env` file:
```
# Ngrok Configuration (uncomment and set your ngrok URL for hot-reload support)
# VITE_NGROK_URL=https://your-subdomain.ngrok-free.app
```

### Step 6: Clear Cache

Run these commands:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## Usage

### Basic Usage (CSS/Assets will work)

1. Start your Laravel server:
   ```bash
   php artisan serve
   ```

2. Start Vite:
   ```bash
   npm run dev
   ```

3. Start ngrok (pointing to your server port):
   ```bash
   ngrok http 8000
   ```

4. Access your site via the ngrok URL!

### With Hot Reload (Optional)

For Vite hot-reload through ngrok:

1. Copy your ngrok URL (e.g., `https://abc123.ngrok-free.app`)
2. Uncomment and set `VITE_NGROK_URL` in your `.env`:
   ```
   VITE_NGROK_URL=https://abc123.ngrok-free.app
   ```
3. Restart `npm run dev`

---

## How It Works

1. **NgrokServiceProvider** detects when requests come through ngrok by checking:
   - `X-Forwarded-Host` header containing "ngrok"
   - `X-Forwarded-Proto` being "https"

2. When ngrok is detected, it:
   - Forces HTTPS scheme for all URL generation
   - Updates the APP_URL to match the ngrok domain
   - Ensures all assets use the correct URLs

3. **TrustProxies middleware** allows Laravel to trust the headers forwarded by ngrok.

---

## Troubleshooting

### CSS/JS not loading
- Make sure `npm run dev` is running
- Check that TrustProxies is configured
- Clear config cache: `php artisan config:clear`

### Mixed content errors
- The NgrokServiceProvider should handle this automatically
- Verify the provider is registered in `bootstrap/providers.php`

### Hot reload not working through ngrok
- Set `VITE_NGROK_URL` in `.env`
- Restart the Vite dev server

---

## Compatibility

- Laravel 11.x
- Vite 5.x
- Works with Tailwind CSS
