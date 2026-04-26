
import './bootstrap';
import Alpine from 'alpinejs';
import QRCode from 'qrcode';
import { Html5QrcodeScanner } from 'html5-qrcode';
import './pwa';
import dashboard from './dashboard';

// Make libraries global
window.Alpine = Alpine;
window.QRCode = QRCode;
window.Html5QrcodeScanner = Html5QrcodeScanner;
window.dashboard = dashboard;

// Global Toast Store
Alpine.store('toast', {
    list: [],
    add(message, type = 'success') {
        const id = Date.now();
        this.list.push({ id, message, type, show: true, progress: 100 });
        setTimeout(() => this.remove(id), 2000);
    },
    remove(id) {
        const index = this.list.findIndex(t => t.id === id);
        if (index !== -1) {
            this.list[index].show = false;
            setTimeout(() => {
                this.list = this.list.filter(t => t.id !== id);
            }, 400);
        }
    }
});

// Start Alpine AFTER all globals are set
Alpine.start();
