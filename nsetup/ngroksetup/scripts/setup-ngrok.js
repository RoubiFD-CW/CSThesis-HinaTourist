
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
const ngrokSetupDir = path.join(projectRoot, 'Nsetup', 'ngroksetup');

// Paths
const paths = {
    providerSrc: path.join(ngrokSetupDir, 'NgrokServiceProvider.php'),
    providerDest: path.join(projectRoot, 'app/Providers/NgrokServiceProvider.php'),
    bootstrapProviders: path.join(projectRoot, 'bootstrap/providers.php'),
    bootstrapApp: path.join(projectRoot, 'bootstrap/app.php'),
    viteConfig: path.join(projectRoot, 'vite.config.js'),
    env: path.join(projectRoot, '.env'),
    envExample: path.join(projectRoot, '.env.example'),
    envSnippet: path.join(ngrokSetupDir, 'env-snippet.txt'),
    packageJson: path.join(projectRoot, 'package.json'),
};

function log(msg) {
    console.log(`[NgrokSetup] ${msg}`);
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

function copyServiceProvider() {
    log('Checking Service Provider...');
    if (fs.existsSync(paths.providerDest)) {
        log('NgrokServiceProvider.php already exists.');
    } else {
        const destDir = path.dirname(paths.providerDest);
        if (!fs.existsSync(destDir)) {
            fs.mkdirSync(destDir, { recursive: true });
        }
        if (fs.existsSync(paths.providerSrc)) {
            fs.copyFileSync(paths.providerSrc, paths.providerDest);
            log('Copied NgrokServiceProvider.php to app/Providers/');
        } else {
            log(`Error: Source file not found: ${paths.providerSrc}`);
        }
    }
}

function updateBootstrapProviders() {
    log('Checking bootstrap/providers.php...');
    if (!fs.existsSync(paths.bootstrapProviders)) {
        log('Error: bootstrap/providers.php not found.');
        return;
    }
    let content = fs.readFileSync(paths.bootstrapProviders, 'utf8');
    if (!content.includes('NgrokServiceProvider::class')) {
        // Insert before the closing bracket of the array
        const insertionPoint = content.lastIndexOf('];');
        if (insertionPoint !== -1) {
            const newContent = content.slice(0, insertionPoint) +
                "    App\\Providers\\NgrokServiceProvider::class,\n" +
                content.slice(insertionPoint);
            fs.writeFileSync(paths.bootstrapProviders, newContent);
            log('Registered NgrokServiceProvider in bootstrap/providers.php');
        } else {
            log('Warning: Could not automatically register provider in bootstrap/providers.php');
        }
    } else {
        log('NgrokServiceProvider already registered.');
    }
}

function updateBootstrapApp() {
    log('Checking bootstrap/app.php...');
    if (!fs.existsSync(paths.bootstrapApp)) {
        log('Error: bootstrap/app.php not found.');
        return;
    }
    let content = fs.readFileSync(paths.bootstrapApp, 'utf8');

    // Add import if missing
    if (!content.includes('use Illuminate\\Http\\Request;')) {
        content = content.replace(
            'use Illuminate\\Foundation\\Application;',
            "use Illuminate\\Foundation\\Application;\nuse Illuminate\\Http\\Request;"
        );
        log('Added Request import to bootstrap/app.php');
    }

    // Add trustProxies if missing
    if (!content.includes('trustProxies')) {
        // Look for withMiddleware closure
        const middlewareRegex = /->withMiddleware\(function\s*\(Middleware\s*\$middleware\):\s*void\s*\{/;
        const match = content.match(middlewareRegex);

        if (match) {
            const insertionStr = `${match[0]}\n        $middleware->trustProxies(at: '*');`;
            content = content.replace(match[0], insertionStr);
            log('Added trustProxies to bootstrap/app.php');
        } else {
            log('Warning: Could not find withMiddleware closure in bootstrap/app.php');
        }
    } else {
        log('trustProxies already configured in bootstrap/app.php');
    }

    fs.writeFileSync(paths.bootstrapApp, content);
}

function checkInstalledPackages() {
    if (!fs.existsSync(paths.packageJson)) return { hasPwa: false, hasTailwind: false };

    try {
        const pkg = JSON.parse(fs.readFileSync(paths.packageJson, 'utf8'));
        const allDeps = { ...pkg.dependencies, ...pkg.devDependencies };
        return {
            hasPwa: !!allDeps['vite-plugin-pwa'],
            hasTailwind: !!allDeps['tailwindcss'] || !!allDeps['@tailwindcss/vite']
        };
    } catch (e) {
        log('Error reading package.json');
        return { hasPwa: false, hasTailwind: false };
    }
}

async function updateViteConfig() {
    log('Checking vite.config.js...');
    if (!fs.existsSync(paths.viteConfig)) {
        log('Error: vite.config.js not found.');
        return;
    }

    let content = fs.readFileSync(paths.viteConfig, 'utf8');

    // Check if configuration seems to contain legacy/complex ngrok logic
    if (content.includes('VITE_NGROK_URL') || content.includes('hmr: ngrokUrl')) {
        log('Notice: vite.config.js contains old Ngrok HMR configuration.');
        log('Replacing config file with clean template (Localhost HMR only)...');

        const answer = await askQuestion('   Do you want to REPLACE your vite.config.js with the clean template? (y/N): ');

        if (answer.toLowerCase() === 'y' || answer.toLowerCase() === 'yes') {
            const { hasPwa, hasTailwind } = checkInstalledPackages();

            // Generate clean config
            let newConfig = `import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
${hasPwa ? "import { VitePWA } from 'vite-plugin-pwa';" : "// import { VitePWA } from 'vite-plugin-pwa';"}
${hasTailwind ? "import tailwindcss from '@tailwindcss/vite';" : "// import tailwindcss from '@tailwindcss/vite';"}

export default defineConfig(({ mode }) => {
    // Load env file based on mode in the current working directory.
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
${hasTailwind ? "            tailwindcss()," : "            // tailwindcss(),"}
${hasPwa ? `            VitePWA({
                outDir: 'public',
                registerType: 'autoUpdate',
                injectRegister: 'auto',
                workbox: {
                    navigateFallback: null,
                    globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
                    globIgnores: ['**/manifest.webmanifest'],
                },
                manifest: {
                    name: env.VITE_APP_NAME || 'Tourist App',
                    short_name: 'App',
                    description: 'My Awesome App',
                    theme_color: '#F00',
                    icons: [
                        {
                            src: '/icon.svg',
                            sizes: 'any',
                            type: 'image/svg+xml',
                            purpose: 'any'
                        }
                    ]
                }
            }),` : "            // VitePWA({...}),"}
        ],
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
            host: '127.0.0.1',
        },
    };
});
`;
            fs.writeFileSync(paths.viteConfig, newConfig);
            log('Replaced vite.config.js with clean template.');
            if (hasPwa) log('Detected and enabled VitePWA.');
            if (hasTailwind) log('Detected and enabled TailwindCSS.');

        } else {
            log('   Skipping vite.config.js update.');
        }
    } else {
        log('vite.config.js seems clean.');
    }
}

async function handleEnv() {
    log('Checking .env configuration...');

    if (!fs.existsSync(paths.env)) {
        if (fs.existsSync(paths.envExample)) {
            log('Creating .env from .env.example...');
            fs.copyFileSync(paths.envExample, paths.env);
            log('Created .env from .env.example');
        } else {
            log('Error: .env.example not found. Cannot create .env.');
        }
    } else {
        log('.env already exists. Skipping creation.');
    }

    // Logic to append Ngrok config has been removed.
}



async function main() {
    log('Starting automatic ngrok setup...');

    try {
        const confirm = await askQuestion('Do you want to proceed with Ngrok Setup (Backend Only)? (y/N): ');
        if (confirm.toLowerCase() === 'y' || confirm.toLowerCase() === 'yes') {
            copyServiceProvider();
            updateBootstrapProviders();
            updateBootstrapApp();
            await updateViteConfig();
            await handleEnv();

            log('\nSetup complete!');
            log('Backend proxies are configured.');

            log('Don\'t forget to run:');
            log('  php artisan config:clear');
            log('  php artisan cache:clear');
        } else {
            log('Setup aborted.');
        }
    } catch (error) {
        console.error('An error occurred during setup:', error);
    } finally {
        rl.close();
    }
}

main();
