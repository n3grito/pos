document.addEventListener('alpine:init', () => {
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            return;
        }

        if (e.ctrlKey || e.metaKey) {
            switch (e.key.toLowerCase()) {
                case 'n':
                    e.preventDefault();
                    const newSaleBtn = document.querySelector('[data-shortcut="new-sale"]') || document.querySelector('a[href*="sales/create"]');
                    if (newSaleBtn) {
                        Livewire.dispatch('openQuickSale');
                    }
                    break;
                case 'p':
                    const newPurchaseBtn = document.querySelector('a[href*="purchases/create"]');
                    if (newPurchaseBtn) {
                        e.preventDefault();
                        window.location.href = newPurchaseBtn.href;
                    }
                    break;
                case 'b':
                    e.preventDefault();
                    const backupBtn = document.querySelector('a[href*="database/backups"]');
                    if (backupBtn) window.location.href = backupBtn.href;
                    break;
                case 'r':
                    e.preventDefault();
                    const reportsBtn = document.querySelector('a[href*="reports"]');
                    if (reportsBtn) window.location.href = reportsBtn.href;
                    break;
                case 'd':
                    e.preventDefault();
                    const dashBtn = document.querySelector('a[href*="dashboard"]');
                    if (dashBtn) window.location.href = dashBtn.href;
                    break;
            }
        }

        if (e.key === 'F1') {
            e.preventDefault();
            const manualLink = document.querySelector('a[href*="manuals"]');
            if (manualLink) window.location.href = manualLink.href;
        }

        if (e.key === 'F5') {
            e.preventDefault();
            const inventoryLink = document.querySelector('a[href*="inventory"]');
            if (inventoryLink) window.location.href = inventoryLink.href;
        }

        if (e.key === 'F8') {
            e.preventDefault();
            const salesLink = document.querySelector('a[href*="sales"]');
            if (salesLink) window.location.href = salesLink.href;
        }
    });
});

export {};
