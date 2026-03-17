
# Nsetup - Universal Configuration Setup

`Nsetup` is a modular configuration tool designed to quickly set up essential features in your Laravel + Vite projects.

## Included Modules

-   **Ngrok Setup**: Configures Laravel and Vite to work seamlessly with Ngrok (HTTPS tunneling, hot-reload support).
-   **PWA Setup**: Configures `vite-plugin-pwa`, Service Workers, and manifests for Progressive Web App support.

---

## How to Use in a New Project

1.  **Copy the `Nsetup` folder** to the **root** of your Laravel project.
    
    Structure should look like:
    ```
    my-laravel-project/
    ├── app/
    ├── bootstrap/
    ├── public/
    ├── Nsetup/          <-- Copied here
    ├── vite.config.js
    └── ...
    ```

2.  **Run the Setup Wizard**
    
    Open your terminal in the project root and run:
    
    ```bash
    node Nsetup/setup.js
    ```

3.  **Follow the Menu**
    
    The wizard will ask what you want to install:
    
    ```
    =================================
          🚀 Nsetup - Project Setup     
    =================================
    1. 🌐 Setup Ngrok Support
    2. 📱 Setup PWA Support
    3. ✨ Setup Both (Sequential)
    4. 🚫 Cancel / Exit
    ```

4.  **Post-Installation**
    
    -   **Ngrok**: 
        -   Add `VITE_NGROK_URL` to your `.env` (instructions provided by script).
        -   Run `php artisan config:clear`.
    -   **PWA**:
        -   Ensure icons are in `public/`.
        -   Add manifest links to your Blade layout `<head>`.
        -   Run `npm install -D vite-plugin-pwa` if you haven't already.
        -   Run `npm run build`.

---

## Individual Scripts

You can also run the individual setup scripts manually if you prefer:

**Ngrok Only:**
```bash
node Nsetup/ngroksetup/scripts/setup-ngrok.js
```

**PWA Only:**
```bash
node Nsetup/pwasetup/scripts/setup-pwa.js
```
