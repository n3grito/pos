const syncQueue = [];

function loadQueue() {
    try {
        const data = localStorage.getItem('pwa_sync_queue');
        if (data) syncQueue.push(...JSON.parse(data));
    } catch {}
}

function saveQueue() {
    localStorage.setItem('pwa_sync_queue', JSON.stringify(syncQueue));
}

export function addToQueue(action, payload) {
    syncQueue.push({ action, payload, timestamp: Date.now() });
    saveQueue();
}

export async function processQueue() {
    if (!navigator.onLine || syncQueue.length === 0) return;

    while (syncQueue.length > 0) {
        const item = syncQueue[0];
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const resp = await fetch('/api/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(item),
            });
            if (resp.ok) {
                syncQueue.shift();
                saveQueue();
            } else {
                break;
            }
        } catch {
            break;
        }
    }
}

if (typeof window !== 'undefined') {
    loadQueue();
    window.addEventListener('online', processQueue);
}
