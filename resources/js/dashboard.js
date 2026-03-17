
export default function dashboard() {
    // Read the config from the hidden JSON script tag
    let configElement = document.getElementById('dashboard-config');
    let cfg = configElement ? JSON.parse(configElement.textContent) : {};

    return {
        online: navigator.onLine,
        // Toast State
        showToast: false,
        toastMessage: '',
        toastType: 'success', // 'success' or 'error'

        showScanner: false,
        showGenerator: false,
        scanner: null,
        generatorArea: cfg.generatorArea || 'General',
        siteUrl: '',
        logs: [],
        pendingLogs: [],

        get allLogs() {
            return [...this.pendingLogs, ...this.logs];
        },

        // Toast Method
        toast(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => this.showToast = false, 3000);
        },

        init() {
            window.addEventListener('online', () => { this.online = true; this.syncLogs(); });
            window.addEventListener('offline', () => this.online = false);

            this.pendingLogs = JSON.parse(localStorage.getItem('pending_logs') || '[]');
            this.logs = JSON.parse(localStorage.getItem('cached_logs') || '[]');

            if (this.online && this.pendingLogs.length > 0) this.syncLogs();
            this.fetchLogs();

            this.$watch('showScanner', value => {
                if (value) this.initScanner();
                else this.stopScanner();
            });
        },

        fetchLogs() {
            // We attempt fetch even if offline, relying on SW cache or eventual failure
            axios.get(cfg.logIndexUrl)
                .then(res => {
                    this.logs = res.data;
                    localStorage.setItem('cached_logs', JSON.stringify(this.logs));
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    // If fetch fails and we have no logs, we rely on init() loaded cache
                });
        },

        initScanner() {
            this.$nextTick(() => {
                if (!this.scanner) {
                    if (!window.Html5QrcodeScanner) {
                        alert('Scanner library not loaded. Refreshing...');
                        return;
                    }
                    this.scanner = new window.Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
                    this.scanner.render(this.onScanSuccess.bind(this), () => { });
                }
            });
        },

        stopScanner() {
            if (this.scanner) {
                this.scanner.clear().catch(e => console.error(e));
                this.scanner = null;
            }
        },

        closeScanner() {
            this.showScanner = false;
        },

        onScanSuccess(decodedText) {
            try {
                const data = JSON.parse(decodedText);
                if (data.visitor_type && data.group_size) {
                    // Restrict QR scans to the attendant's assigned area
                    if (!cfg.isAdmin && cfg.dedicatedArea && data.dedicated_area) {
                        if (data.dedicated_area.toLowerCase() !== cfg.dedicatedArea.toLowerCase()) {
                            this.toast(`Upload Rejected: This pass is valid for ${data.dedicated_area}, not here.`, 'error');
                            this.closeScanner();
                            return;
                        }
                    }

                    this.saveLog(data);
                    this.closeScanner();
                    this.toast('Visitor Verified & Logged: ' + data.origin, 'success');
                } else {
                    this.toast('Invalid QR Code.', 'error');
                }
            } catch (e) {
                console.error(e);
                this.toast('Error reading QR Code.', 'error');
            }
        },

        openGenerator() {
            this.showGenerator = true;
            this.$nextTick(() => this.generateSiteQR());
        },

        generateSiteQR() {
            const area = this.generatorArea;
            this.siteUrl = `${cfg.visitorPassUrl}?area=${encodeURIComponent(area)}`;
            const canvas = document.getElementById('site-qr-canvas');
            if (window.QRCode) {
                // Determine current URL for absolute reference if needed, but QRCode uses exact string.
                window.QRCode.toCanvas(canvas, this.siteUrl, { width: 200 }, (err) => {
                    if (err) console.error(err);
                });
            }
        },

        printQR() {
            const canvas = document.getElementById('site-qr-canvas');
            const imgUrl = canvas.toDataURL("image/png");
            const win = window.open('', '_blank');

            const scriptTag = '<' + 'script>window.print();</' + 'script>';

            const html = `<html><head><title>Print QR</title></head>
                <body style="text-align:center;font-family:sans-serif;padding:50px;">
                <h1>Scan to Log In</h1>
                <h2>${this.generatorArea}</h2><br/>
                <img src="${imgUrl}" style="width:400px;"/><br/><br/>
                <p>Scan to verify entry.</p>
                ${scriptTag}
                </body></html>`;

            win.document.write(html);
            win.document.close();
        },

        saveLog(data) {
            const logEntry = {
                ...data,
                visit_reason_other: data.visit_reason_other || '',
                dedicated_area: data.dedicated_area || cfg.dedicatedArea || 'General',
                local_id: Date.now(),
                visit_date: new Date().toISOString(),
                created_at: new Date().toISOString(),
                pending: true
            };

            this.pendingLogs.unshift(logEntry);
            this.saveToStorage();

            if (this.online) this.syncLogs();
        },

        syncLogs() {
            if (this.pendingLogs.length === 0) return;
            const queue = this.pendingLogs.filter(log => !log.syncing);
            queue.forEach(log => {
                log.syncing = true;
                axios.post(cfg.logStoreUrl, log)
                    .then(res => {
                        this.pendingLogs = this.pendingLogs.filter(l => l.local_id !== log.local_id);
                        this.saveToStorage();

                        // Avoid adding duplicates if fetchLogs already retrieved it
                        if (!this.logs.some(l => l.id === res.data.log.id)) {
                            this.logs.unshift(res.data.log);
                        }
                    })
                    .catch(err => {
                        console.error('Sync failed:', err);
                        if (err.response && err.response.status === 422) {
                            // Invalid payload, discard to prevent infinite retry loop
                            this.pendingLogs = this.pendingLogs.filter(l => l.local_id !== log.local_id);
                            this.saveToStorage();
                            if (typeof this.toast === 'function') {
                                this.toast('A pending log had invalid data and was discarded.', 'error');
                            }
                        } else {
                            log.syncing = false;
                        }
                    });
            });
        },

        saveToStorage() {
            localStorage.setItem('pending_logs', JSON.stringify(this.pendingLogs));
        }
    };
}
