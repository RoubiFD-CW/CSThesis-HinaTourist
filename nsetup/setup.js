
import fs from 'fs';
import path from 'path';
import readline from 'readline';
import { fileURLToPath } from 'url';
import { spawn } from 'child_process';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Paths
const scripts = {
    ngrok: path.join(__dirname, 'ngroksetup', 'scripts', 'setup-ngrok.js'),
    pwa: path.join(__dirname, 'pwasetup', 'scripts', 'setup-pwa.js'),
};

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

// Handle Ctrl+C gracefully
rl.on('SIGINT', () => {
    console.log('\n\nOperation cancelled by user. Exiting...');
    process.exit(0);
});

const runScript = (scriptName, scriptPath) => {
    return new Promise((resolve) => {
        if (!fs.existsSync(scriptPath)) {
            console.error(`\nError: Script not found: ${scriptPath}`);
            resolve(1);
            return;
        }

        console.log(`\n[ ${scriptName} ] Starting...`);

        // Pause parent readline to prevent input conflicts
        rl.pause();

        const child = spawn(process.execPath, [scriptPath], { stdio: 'inherit', shell: false });

        child.on('close', (code) => {
            console.log(`\n[ ${scriptName} ] Finished (Exit Code: ${code}).`);
            rl.resume(); // Resume parent readline
            resolve(code);
        });

        child.on('error', (err) => {
            console.error(`\nFailed to run ${scriptName}: ${err.message}`);
            rl.resume(); // Resume here too
            resolve(1);
        });
    });
};

const showMenu = () => {
    console.log('\n---------------------------------');
    console.log('      NSETUP - PROJECT SETUP     ');
    console.log('---------------------------------');
    console.log('1. Setup Ngrok Support');
    console.log('2. Setup PWA Support');
    console.log('3. Setup Both (Sequential)');
    console.log('4. Cancel / Exit');
    console.log('---------------------------------');

    rl.question('\nSelect an option (1-4): ', async (answer) => {
        const choice = answer.trim();

        switch (choice) {
            case '1':
                await runScript('Ngrok Setup', scripts.ngrok);
                promptContinue();
                break;
            case '2':
                await runScript('PWA Setup', scripts.pwa);
                promptContinue();
                break;
            case '3':
                console.log('\n--- Step 1: Ngrok Setup ---');
                await runScript('Ngrok Setup', scripts.ngrok);
                console.log('\n--- Step 2: PWA Setup ---');
                await runScript('PWA Setup', scripts.pwa);
                promptContinue();
                break;
            case '4':
            case 'exit':
            case 'cancel':
                exitScript();
                break;
            default:
                console.log('Invalid option. Please try again.');
                showMenu();
        }
    });
};

const promptContinue = () => {
    rl.question('\nPress ENTER to return to menu, or type "exit" to quit: ', (answer) => {
        if (answer.toLowerCase().trim() === 'exit') {
            exitScript();
        } else {
            showMenu();
        }
    });
};

const exitScript = () => {
    console.log('Exiting Nsetup.');
    rl.close();
    process.exit(0);
};

// Start
// Clear screen for a fresh start (optional, but nice for "interactive" feel)
console.log('\x1Bc');
showMenu();
