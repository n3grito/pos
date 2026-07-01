import { db } from './db';

class PwaManager {
    constructor() {
        this.deferredPrompt = null;
        this.isOnline = navigator.onLine;
        this.syncing = false;
        this._bindEvents();
    }

    _bindEvents() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this._updateOnlineStatus();
            this.processQueue();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this._updateOnlineStatus();
        });

        window.addEventListener('beforeinstallprompt', event => {
            this.deferredPrompt = event;
        });

        window.addEventListener('appinstalled', () => {
            this.deferredPrompt = null;
        });

        navigator.serviceWorker.addEventListener('message', event => {
            if (event.data && event.data.type === 'PROCESS_SYNC_QUEUE') {
                this.processQueue();
            }
        });
    }

    _updateOnlineStatus() {
        document.documentElement.classList.toggle('offline', !this.isOnline);

        const indicators = document.querySelectorAll('[data-online-indicator]');
        indicators.forEach(el => {
            el.classList.toggle('hidden', this.isOnline);
            el.classList.toggle('flex', !this.isOnline);
        });

        const onlineIndicators = document.querySelectorAll('[data-offline-indicator]');
        onlineIndicators.forEach(el => {
            el.classList.toggle('hidden', !this.isOnline);
            el.classList.toggle('flex', this.isOnline);
        });
    }

    async queueSale(saleData) {
        const sale = {
            items: saleData.cart.map(item => ({
                product_id: item.product_id,
                quantity: item.quantity,
                price: item.price,
            })),
            payment_method: saleData.paymentMethod || 'cash',
            amount_paid: saleData.amountPaid || 0,
            client_name: saleData.clientName || '',
            client_nit: saleData.clientNit || '',
            payment_reference: saleData.paymentReference || '',
            client_id: saleData.clientId || null,
        };

        await db.queueSale(sale);

        if (this.isOnline) {
            await this.processQueue();
        } else if ('serviceWorker' in navigator && 'sync' in ServiceWorkerRegistration.prototype) {
            try {
                const registration = await navigator.serviceWorker.ready;
                await registration.sync.register('sync-sales');
            } catch (e) {
                console.warn('Background Sync not available, will sync on next online event');
            }
        }
    }

    async processQueue() {
        if (!this.isOnline || this.syncing) return;
        this.syncing = true;

        try {
            const queuedSales = await db.getQueuedSales();

            for (const sale of queuedSales) {
                try {
                    const response = await fetch('/sales', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        body: JSON.stringify(sale),
                    });

                    if (response.ok) {
                        await db.removeQueuedSale(sale.id);
                    } else {
                        const text = await response.text();
                        console.warn('Sync failed for sale', sale.id, response.status, text);
                        break;
                    }
                } catch (e) {
                    console.warn('Sync network error, will retry later', e);
                    break;
                }
            }
        } catch (e) {
            console.error('Error processing sync queue:', e);
        } finally {
            this.syncing = false;
        }
    }

    async getQueuedCount() {
        try {
            const sales = await db.getQueuedSales();
            return sales.length;
        } catch {
            return 0;
        }
    }

    async showInstallPrompt() {
        if (!this.deferredPrompt) return false;
        this.deferredPrompt.prompt();
        const result = await this.deferredPrompt.userChoice;
        this.deferredPrompt = null;
        return result.outcome === 'accepted';
    }

    async cacheData(products, customers) {
        if (!this.isOnline) return;
        try {
            if (products && products.length) await db.cacheProducts(products);
            if (customers && customers.length) await db.cacheCustomers(customers);
        } catch (e) {
            console.warn('Failed to cache data:', e);
        }
    }
}

export const pwa = new PwaManager();
