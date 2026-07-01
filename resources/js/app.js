import { initDashboardCharts } from './dashboard-charts';
import './keyboard-shortcuts';
import echo from './echo';
import { pwa } from './pwa';

function showToast(message, type = 'success', persistent = false) {
    const tm = document.querySelector('[x-data="toastManager()"]');
    if (tm && tm.__x) {
        tm.__x.$data.addToast(message, type, persistent);
    }
}

window.showToast = showToast;

window.Alpine.data('toastManager', () => ({
    toasts: [],
    init() {
        this.loadFromSession();
        document.addEventListener('livewire:navigated', () => this.loadFromSession());
        window.addEventListener('notify', e => {
            this.addToast(e.detail.message, e.detail.type || 'success', false);
        });
    },
    loadFromSession() {
        const el = this.$el;
        const sessionToasts = JSON.parse(el.dataset.sessionToasts || '[]');
        sessionToasts.forEach(t => this.addToast(t.message, t.type, t.persistent || false));
        if (el.dataset.sessionSuccess) this.addToast(el.dataset.sessionSuccess, 'success');
        if (el.dataset.sessionError) this.addToast(el.dataset.sessionError, 'error');
        if (el.dataset.sessionWarning) this.addToast(el.dataset.sessionWarning, 'warning');
        if (el.dataset.sessionInfo) this.addToast(el.dataset.sessionInfo, 'info');
    },
    addToast(message, type = 'success', persistent = false) {
        const toast = { message, type, visible: true };
        this.toasts.push(toast);
        if (!persistent) {
            setTimeout(() => this.removeToast(this.toasts.indexOf(toast)), 5000);
        }
    },
    removeToast(index) {
        if (this.toasts[index]) {
            this.toasts[index].visible = false;
            setTimeout(() => { this.toasts.splice(index, 1); }, 300);
        }
    },
}));

window.Alpine.data('offlineIndicator', () => ({
    isOnline: navigator.onLine,
    queuedCount: 0,
    async init() {
        this.updateStatus();
        window.addEventListener('online', () => { this.isOnline = true; this.updateCount(); });
        window.addEventListener('offline', () => { this.isOnline = false; });
        setInterval(() => this.updateCount(), 30000);
        document.addEventListener('livewire:navigated', () => this.updateCount());
    },
    updateStatus() {
        this.isOnline = navigator.onLine;
    },
    async updateCount() {
        this.queuedCount = await pwa.getQueuedCount();
    },
}));

window.Alpine.data('installPrompt', () => ({
    show: false,
    async init() {
        await this.check();
        window.addEventListener('beforeinstallprompt', () => this.check());
    },
    async check() {
        const prompt = window.pwa?.deferredPrompt;
        this.show = !!prompt && !window.matchMedia('(display-mode: standalone)').matches;
    },
    async install() {
        if (window.pwa) {
            const accepted = await window.pwa.showInstallPrompt();
            if (accepted) this.show = false;
        }
    },
}));

window.pwa = pwa;

pwa._updateOnlineStatus();

document.addEventListener('DOMContentLoaded', () => {
    initDashboardCharts();

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }

    const pollerData = document.getElementById('activityPollerData');
    if (pollerData) {
        const streamUrl = pollerData.dataset.streamUrl;
        setInterval(() => {
            const badge = document.getElementById('sidebarAlertBadge');
            if (!badge) return;

            fetch(streamUrl + '?since=30')
                .then(r => r.json())
                .then(data => {
                    if (data.count > 0) {
                        badge.classList.remove('hidden');
                        data.alerts.forEach(alert => {
                            showToast(
                                alert.description,
                                alert.severity === 'critical' ? 'error' : 'warning'
                            );
                        });
                        setTimeout(() => badge.classList.add('hidden'), 30000);
                    }
                })
                .catch(() => {});
        }, 15000);
    }

    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (userId && echo) {
        echo.private('user.' + userId)
            .listen('.SaleCompleted', e => showToast(e.message, 'success'));

        echo.private('admin.notifications')
            .listen('.LowStockAlert', e =>
                showToast(e.message, e.severity === 'critical' ? 'error' : 'warning')
            );
    }
});
