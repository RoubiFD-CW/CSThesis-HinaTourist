
import fs from 'fs';
import path from 'path';
import readline from 'readline';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

const projectRoot = path.resolve(__dirname, '..', '..', '..');
const paths = {
    packageJson: path.join(projectRoot, 'package.json'),
    viteConfig: path.join(projectRoot, 'vite.config.js'),
    resourcesJsDir: path.join(projectRoot, 'resources/js'),
    appJs: path.join(projectRoot, 'resources/js/app.js'),
    publicDir: path.join(projectRoot, 'public'),
};

function log(msg) {
    console.log(`[PWASetup] ${msg}`);
}

function askQuestion(query) {
    return new Promise(resolve => {
        rl.question(query, (answer) => {
            if (answer.trim().toLowerCase() === 'cancel') {
                console.log('\nSetup cancelled by user.');
                process.exit(0);
            }
            resolve(answer);
        });
    });
}

function logStep(step) {
    console.log(`\n[PWASetup] --- Step: ${step} ---`);
}

async function checkDependencies() {
    logStep('Dependencies Check');
    if (fs.existsSync(paths.packageJson)) {
        const pkg = JSON.parse(fs.readFileSync(paths.packageJson, 'utf8'));
        const deps = { ...pkg.dependencies, ...pkg.devDependencies };
        if (!deps['vite-plugin-pwa']) {
            log('Notice: vite-plugin-pwa is MISSING.');
            log('Please run: npm install -D vite-plugin-pwa');
        } else {
            log('vite-plugin-pwa is installed.');
        }
    } else {
        log('Error: package.json not found.');
    }
}

async function collectPwaConfig() {
    console.log('\n--- Interactive PWA Configuration ---');
    console.log('(Type "cancel" at any time to exit)\n');

    const config = {
        name: 'My PWA App',
        short_name: 'App',
        description: 'My Awesome App',
        theme_color: '#FF2D20',
    };

    const name = await askQuestion(`App Name [${config.name}]: `);
    if (name.trim()) config.name = name.trim();

    const shortName = await askQuestion(`Short Name [${config.short_name}]: `);
    if (shortName.trim()) config.short_name = shortName.trim();

    const desc = await askQuestion(`Description [${config.description}]: `);
    if (desc.trim()) config.description = desc.trim();

    const theme = await askQuestion(`Theme Color [${config.theme_color}]: `);
    if (theme.trim()) config.theme_color = theme.trim();

    console.log('\n--- Icons ---');
    console.log('Ensure you place your icons in public/ directory.');
    console.log('Standard names: icon.svg (any), android-chrome-192x192.png, android-chrome-512x512.png');

    return config;
}

async function setupViteConfig(config) {
    logStep('Vite Configuration');
    if (!fs.existsSync(paths.viteConfig)) {
        log('Error: vite.config.js not found.');
        return;
    }

    // Default Config
    let pwaConfig = {
        name: 'My PWA App',
        short_name: 'App',
        description: 'My Awesome App',
        theme_color: '#FF2D20',
    };

    if (config) {
        pwaConfig = { ...pwaConfig, ...config };
    }

    let content = fs.readFileSync(paths.viteConfig, 'utf8');

    let modified = false;

    // 1. Add Import
    if (!content.includes('vite-plugin-pwa')) {
        log('Adding VitePWA import...');
        if (content.includes("import { defineConfig } from 'vite';")) {
            content = content.replace(
                "import { defineConfig } from 'vite';",
                "import { defineConfig } from 'vite';\nimport { VitePWA } from 'vite-plugin-pwa';"
            );
            modified = true;
        } else {
            // Fallback: prepend
            content = "import { VitePWA } from 'vite-plugin-pwa';\n" + content;
            modified = true;
        }
    } else {
        log('VitePWA import present.');
    }

    // 2. Add Plugin Config
    // If it already exists, implementing a full parser to replace it is complex.
    // For now, we only inject if missing. If present, we warn user to update manually.
    if (!content.includes('VitePWA({')) {
        log('Adding VitePWA configuration...');
        const pluginConfig = `
            VitePWA({
                outDir: 'public',
                registerType: 'autoUpdate',
                injectRegister: 'auto',
                workbox: {
                    navigateFallback: null,
                    globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
                    globIgnores: ['**/manifest.webmanifest'],
                },
                manifest: {
                    name: ${JSON.stringify(pwaConfig.name)},
                    short_name: ${JSON.stringify(pwaConfig.short_name)},
                    description: ${JSON.stringify(pwaConfig.description)},
                    theme_color: ${JSON.stringify(pwaConfig.theme_color)},
                    start_url: '/',
                    scope: '/',
                    display: 'standalone',
                    icons: [
                        {
                            src: '/icon.svg',
                            sizes: 'any',
                            type: 'image/svg+xml',
                            purpose: 'any'
                        }
                    ]
                }
            })`;

        const pluginsRegex = /plugins:\s*\[/;
        const match = content.match(pluginsRegex);

        if (match) {
            content = content.replace(pluginsRegex, `plugins: [\n${pluginConfig},`);
            modified = true;
            log('Injected VitePWA config into plugins array.');
        } else {
            log('Warning: Could not find plugins array in vite.config.js. Please add VitePWA manually.');
        }
    } else {
        log('Notice: VitePWA configuration already present in vite.config.js.');
        log('Skipping overwrite. Please update values manually if needed.');
    }

    if (modified) {
        fs.writeFileSync(paths.viteConfig, content);
        log('Updated vite.config.js');
    }
}

async function setupServiceWorkerRegistration() {
    logStep('Service Worker Registration');

    const pwaJsPath = path.join(paths.resourcesJsDir, 'pwa.js');
    const pwaContent = `
// PWA Service Worker Registration
// This script registers the service worker generated by vite-plugin-pwa.

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // Registers /sw.js (default output of vite-plugin-pwa)
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}
`;
    // Create pwa.js if not exists
    if (!fs.existsSync(pwaJsPath)) {
        fs.writeFileSync(pwaJsPath, pwaContent);
        log(`Created ${pwaJsPath}`);
    } else {
        log(`${pwaJsPath} already exists.`);
    }

    // Import into app.js
    if (fs.existsSync(paths.appJs)) {
        let appContent = fs.readFileSync(paths.appJs, 'utf8');
        // Check for import
        if (!appContent.includes('./pwa') && !appContent.includes("import './pwa'")) {
            // Append to end
            fs.appendFileSync(paths.appJs, "\nimport './pwa';\n");
            log(`Added "import './pwa';" to app.js`);
        } else {
            log(`app.js already imports pwa.js`);
        }
    } else {
        log(`Warning: resources/js/app.js not found.`);
    }
}

async function setupPartialHead(config) {
    logStep('Partial Head setup (Blade)');
    const viewsDir = path.join(projectRoot, 'resources/views');
    // Check various partial directories
    // User asked to check for "partial folder".
    // We will look for 'partials' or 'partial'.
    const partialsDir = path.join(viewsDir, 'partials');
    const partialDir = path.join(viewsDir, 'partial');

    // Logic: 
    // "check if there is partial folder if exist create directly a head.blade.php"
    // "if exist" -> if the folder exists.

    let targetDir = null;
    if (fs.existsSync(partialsDir)) {
        targetDir = partialsDir;
    } else if (fs.existsSync(partialDir)) {
        targetDir = partialDir;
    } else {
        // Create 'partials' folder automatically if neither exists
        fs.mkdirSync(partialsDir, { recursive: true });
        targetDir = partialsDir;
        log(`Created directory: ${partialsDir}`);
    }

    if (targetDir) {
        const headPath = path.join(targetDir, 'head.blade.php');
        const pwaTags = `<!-- PWA Manifest -->
<link rel="manifest" href="/build/manifest.webmanifest">
<meta name="theme-color" content="${config.theme_color}">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
`;
        const viteTag = `@vite(['resources/css/app.css', 'resources/js/app.js'])`;

        const fullContent = `${pwaTags}\n${viteTag}\n`;

        if (!fs.existsSync(headPath)) {
            fs.writeFileSync(headPath, fullContent);
            log(`Created ${headPath} with PWA tags and @vite`);
        } else {
            log(`${headPath} already exists.`);
            let content = fs.readFileSync(headPath, 'utf8');
            let modified = false;

            if (!content.includes('manifest.webmanifest')) {
                content += '\n' + pwaTags;
                modified = true;
                log(`Appended PWA tags to ${headPath}`);
            }

            if (!content.includes('@vite')) {
                content += '\n' + viteTag;
                modified = true;
                log(`Appended @vite to ${headPath}`);
            }

            if (modified) fs.writeFileSync(headPath, content);
        }
    } else {
        log('Notice: No "partial" or "partials" folder found. Skipping head.blade.php creation.');
    }
}

async function generateDemoIcon(config) {
    logStep('Demo Icon Generation');
    const publicDir = paths.publicDir;
    const iconPath = path.join(publicDir, 'icon.svg');

    const text = config.short_name ? config.short_name.substring(0, 2).toUpperCase() : 'APP';
    const color = config.theme_color || '#000000';

    const svgContent = `<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="${color}"/>
    <path fill="white" d="M129.6 230.1L257.7 448H432L297.4 222.6L129.6 230.1ZM264.4 209.6L169.6 51.2H126.9L219.8 206.5L264.4 209.6ZM421.4 64.2L297.2 201L463 212.5L421.4 64.2ZM242 41.6L205.6 137.4L376.6 64.2H242V41.6Z" transform="translate(40, 40) scale(0.85)"/>
</svg>`;

    fs.writeFileSync(iconPath, svgContent);
    log(`Generated demo icon (Laravel Logo) at ${iconPath}`);
    log('Note: You may need to convert this to PNG for full browser support or update your manifest to point to icon.svg.');
}

async function main() {
    log('Starting PWA Setup...');

    let config = null;

    try {
        await checkDependencies();

        const answer = await askQuestion('Do you want to proceed with PWA setup? (y/N): ');
        if (answer.toLowerCase() === 'y' || answer.toLowerCase() === 'yes') {

            const mode = await askQuestion('Run in interactive mode to customize name, theme, etc? (y/N): ');

            if (mode.toLowerCase() === 'y' || mode.toLowerCase() === 'yes') {
                config = await collectPwaConfig();

                console.log('\n--- Configuration Summary ---');
                console.log(`Name: ${config.name}`);
                console.log(`Short Name: ${config.short_name}`);
                console.log(`Description: ${config.description}`);
                console.log(`Theme Color: ${config.theme_color}`);
                console.log('-----------------------------\n');

                const confirm = await askQuestion('Do you want to start setup now? (y/N): ');
                if (confirm.toLowerCase() !== 'y' && confirm.toLowerCase() !== 'yes') {
                    console.log('Setup cancelled by user.');
                    return;
                }
            } else {
                console.log('Using default configuration...');
                config = {
                    name: 'My PWA App',
                    short_name: 'App',
                    description: 'My Awesome App',
                    theme_color: '#FF2D20',
                };
                // Optional: confirm even for default?
                const confirm = await askQuestion('Do you want to start setup with defaults? (y/N): ');
                if (confirm.toLowerCase() !== 'y' && confirm.toLowerCase() !== 'yes') {
                    console.log('Setup cancelled by user.');
                    return;
                }
            }

            // Proceed
            await setupViteConfig(config);
            await setupServiceWorkerRegistration();
            await setupPartialHead(config);

            const iconAns = await askQuestion('Generate a demo text icon? (y/N): ');
            if (iconAns.toLowerCase() === 'y' || iconAns.toLowerCase() === 'yes') {
                await generateDemoIcon(config);
            }

            log('\nSetup Complete');
            log('Next steps:');
            log('1. Ensure you have icons in your public directory (icon.svg, android-chrome-192x192.png, etc).');
            log('2. Ensure your Blade layout <head> includes:');
            log('   <link rel="manifest" href="/build/manifest.webmanifest">');
            log(`   <meta name="theme-color" content="${config ? config.theme_color : '#ffffff'}">`);
            log('3. Run `npm run build` to generate the service worker and manifest.');

        } else {
            log('Setup aborted.');
        }

    } catch (error) {
        console.error('Error during PWA setup:', error);
    } finally {
        rl.close();
    }
}

main();
