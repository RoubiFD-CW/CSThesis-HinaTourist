import axios from 'axios';

export default function dashboard() {
    // Read the config from the hidden JSON script tag
    let configElement = document.getElementById('dashboard-config');
    let cfg = configElement ? JSON.parse(configElement.textContent) : {};

    return {
        online: navigator.onLine,


        showScanner: false,
        showGenerator: false,
        scanner: null,
        generatorArea: cfg.generatorArea || 'General',
        siteUrl: '',
        logs: [],
        pendingLogs: [],
        currentPage: 1,
        itemsPerPage: 5,

        get allLogs() {
            return [...this.pendingLogs, ...this.logs];
        },

        get paginatedLogs() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.allLogs.slice(start, start + this.itemsPerPage);
        },

        get totalPages() {
            return Math.ceil(this.allLogs.length / this.itemsPerPage);
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        // Toast Method
        toast(message, type = 'success') {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message, type } }));
        },

        init() {
            // Instant detection
            window.addEventListener('online', () => {
                this.online = true;
                this.syncLogs(); // Fire immediately
            });
            window.addEventListener('offline', () => this.online = false);

            // Faster periodic check (Every 2 seconds)
            setInterval(() => {
                if (navigator.onLine !== this.online) {
                    this.online = navigator.onLine;
                    if (this.online) {
                        this.syncLogs();
                        this.fetchLogs();
                    }
                }
            }, 2000);

            this.pendingLogs = JSON.parse(localStorage.getItem('pending_logs') || '[]');
            this.logs = JSON.parse(localStorage.getItem('cached_logs') || '[]');

            if (this.online && this.pendingLogs.length > 0) this.syncLogs();
            this.fetchLogs();

            // Auto-refresh dash every 8 seconds instead of 10
            setInterval(() => {
                if (this.online) {
                    this.fetchLogs();
                    if (this.pendingLogs.length > 0) this.syncLogs();
                }
            }, 8000);

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
            this.siteUrl = `${cfg.visitorPassUrl}?area=${encodeURIComponent(area)}&name=${encodeURIComponent(area)}`;
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
            const logoUrl = window.location.origin + '/hinatourist-logo.png';

            // Clean up any existing modal to avoid duplicates
            const existing = document.getElementById('qr-print-modal-container');
            if (existing) existing.remove();

            const modalContainer = document.createElement('div');
            modalContainer.id = 'qr-print-modal-container';

            modalContainer.innerHTML = `
                <style>
                    .print-modal-backdrop {
                        position: fixed;
                        inset: 0;
                        background: rgba(15, 23, 42, 0.7);
                        backdrop-filter: blur(8px);
                        z-index: 999999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: 20px;
                        font-family: 'Inter', sans-serif;
                    }
                    .print-modal-content {
                        background: #f8fafc;
                        border-radius: 16px;
                        width: 100%;
                        max-width: 500px;
                        max-height: 95vh;
                        overflow-y: auto;
                        position: relative;
                        display: flex;
                        flex-direction: column;
                        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                        animation: modalIn 0.3s ease-out;
                        /* Hide scrollbar for IE, Edge and Firefox */
                        -ms-overflow-style: none;
                        scrollbar-width: none;
                    }
                    /* Hide scrollbar for Chrome, Safari and Opera */
                    .print-modal-content::-webkit-scrollbar {
                        display: none;
                    }
                    @keyframes modalIn {
                        from { opacity: 0; transform: scale(0.95) translateY(10px); }
                        to { opacity: 1; transform: scale(1) translateY(0); }
                    }
                    .print-modal-header {
                        padding: 16px 24px;
                        border-bottom: 1px solid #e2e8f0;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        background: white;
                        border-radius: 16px 16px 0 0;
                    }
                    .print-modal-body {
                        padding: 30px;
                        text-align: center;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                    }
                    .poster-card {
                        background: #ffffff;
                        border: 1px solid #e2e8f0;
                        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                        border-radius: 12px;
                        padding: 40px 30px;
                        width: 100%;
                    }
                    .poster-logo {
                        width: 65px;
                        margin-bottom: 20px;
                    }
                    .poster-h1 {
                        color: #008080;
                        font-size: 28px;
                        font-weight: 800;
                        margin: 0 0 5px 0;
                        letter-spacing: -0.5px;
                        text-transform: uppercase;
                    }
                    .poster-h2 {
                        color: #006666;
                        font-size: 22px;
                        font-weight: 700;
                        margin: 0 0 30px 0;
                    }
                    .poster-qr-container {
                        border: 2px dashed #008080;
                        border-radius: 16px;
                        padding: 20px;
                        display: inline-block;
                        background: #ffffff;
                        margin-bottom: 25px;
                    }
                    .poster-qr-img {
                        width: 220px;
                        height: auto;
                        display: block;
                    }
                    .poster-instructions {
                        color: #475569;
                        font-size: 15px;
                        font-weight: 500;
                    }
                    .print-modal-footer {
                        padding: 16px 24px;
                        border-top: 1px solid #e2e8f0;
                        display: flex;
                        justify-content: flex-end;
                        background: white;
                        border-radius: 0 0 16px 16px;
                    }
                    .btn-print {
                        background: #008080;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                        transition: all 0.2s;
                    }
                    .btn-print:hover {
                        background: #006666;
                        transform: translateY(-1px);
                        box-shadow: 0 4px 6px -1px rgba(0, 128, 128, 0.2);
                    }
                    .btn-close {
                        background: none;
                        border: none;
                        color: #94a3b8;
                        font-size: 24px;
                        cursor: pointer;
                        padding: 0;
                        line-height: 1;
                        transition: color 0.2s;
                    }
                    .btn-close:hover {
                        color: #475569;
                    }
                    
                    /* The Magic Print CSS */
                    @media print {
                        body > *:not(#qr-print-modal-container) {
                            display: none !important;
                        }
                        body {
                            background: white !important;
                            margin: 0 !important;
                            padding: 0 !important;
                        }
                        #qr-print-modal-container {
                            position: static !important;
                            display: block !important;
                        }
                        .print-modal-backdrop {
                            position: static !important;
                            background: white !important;
                            padding: 0 !important;
                            display: block !important;
                        }
                        .print-modal-content {
                            box-shadow: none !important;
                            border: none !important;
                            max-width: none !important;
                            border-radius: 0 !important;
                        }
                        .print-modal-header, .print-modal-footer {
                            display: none !important;
                        }
                        .print-modal-body {
                            padding: 0 !important;
                            background: white !important;
                        }
                        .poster-card {
                            border: none !important;
                            box-shadow: none !important;
                            padding: 40px !important;
                        }
                        .poster-qr-container {
                            border: 3px dashed #008080 !important;
                        }
                        .poster-qr-img {
                            width: 350px !important; /* Scale up slightly for print */
                        }
                        @page { size: portrait; margin: 0; }
                    }
                </style>
                <div class="print-modal-backdrop" id="print-modal-backdrop">
                    <div class="print-modal-content">
                        <div class="print-modal-header">
                            <h3 style="margin:0; font-size:16px; font-weight:700; color:#1e293b;">Print Preview</h3>
                            <button class="btn-close" id="print-modal-close" title="Close">&times;</button>
                        </div>
                        <div class="print-modal-body">
                            <!-- Paper Poster Element -->
                            <div class="poster-card">
                                <img src="${logoUrl}" class="poster-logo" alt="Logo" onerror="this.style.display='none'"/>
                                <h1 class="poster-h1">Scan for Entry</h1>
                                <h2 class="poster-h2">${this.generatorArea}</h2>
                                <div class="poster-qr-container">
                                    <img src="${imgUrl}" class="poster-qr-img" />
                                </div>
                                <p class="poster-instructions">Scan to verify entry.</p>
                            </div>
                        </div>
                        <div class="print-modal-footer">
                            <button class="btn-print" id="print-modal-trigger">
                                <i class="fa-solid fa-print"></i> Print Again
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modalContainer);

            const executePrint = () => {
                const originalTitle = document.title;
                document.title = "Site QR - " + this.generatorArea;
                window.print();
                document.title = originalTitle;
            };

            // Bind interactions
            document.getElementById('print-modal-close').addEventListener('click', () => modalContainer.remove());
            document.getElementById('print-modal-backdrop').addEventListener('click', (e) => {
                if (e.target.id === 'print-modal-backdrop') modalContainer.remove();
            });
            document.getElementById('print-modal-trigger').addEventListener('click', executePrint);

            // Auto-trigger print since base64 image is already fully loaded
            setTimeout(executePrint, 150);
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
