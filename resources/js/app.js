import Alpine from 'alpinejs';
import { initDashboardCharts } from './dashboard-charts';

window.Alpine = Alpine;

Alpine.data('toastManager', () => ({
    toasts: [],
    init() {
        this.loadFromSession();
        document.addEventListener('livewire:navigated', () => this.loadFromSession());
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
    showAlert(message, severity) {
        const type = severity === 'critical' ? 'error' : severity === 'warning' ? 'warning' : 'info';
        this.addToast(message, type, false);
    },
    removeToast(index) {
        if (this.toasts[index]) {
            this.toasts[index].visible = false;
            setTimeout(() => { this.toasts.splice(index, 1); }, 300);
        }
    },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initDashboardCharts();

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
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
                            const tm = document.querySelector('[x-data="toastManager()"]');
                            if (tm && tm.__x) {
                                tm.__x.$data.addToast(
                                    alert.description,
                                    alert.severity === 'critical' ? 'error' : 'warning',
                                    false
                                );
                            }
                        });
                        setTimeout(() => badge.classList.add('hidden'), 30000);
                    }
                })
                .catch(() => {});
        }, 15000);
    }
});
