
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

// Start Alpine AFTER all globals are set
Alpine.start();
