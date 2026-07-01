const DB_NAME = 'pos-offline';
const DB_VERSION = 1;

let _db = null;

function openDB() {
    return new Promise((resolve, reject) => {
        if (_db) return resolve(_db);

        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = event => {
            const db = event.target.result;

            if (!db.objectStoreNames.contains('queued_sales')) {
                const store = db.createObjectStore('queued_sales', { keyPath: 'id', autoIncrement: true });
                store.createIndex('status', 'status', { unique: false });
                store.createIndex('created_at', 'created_at', { unique: false });
            }

            if (!db.objectStoreNames.contains('cached_products')) {
                const store = db.createObjectStore('cached_products', { keyPath: 'id' });
                store.createIndex('sku', 'sku', { unique: true });
                store.createIndex('barcode', 'barcode', { unique: true });
                store.createIndex('updated_at', 'updated_at', { unique: false });
            }

            if (!db.objectStoreNames.contains('cached_customers')) {
                const store = db.createObjectStore('cached_customers', { keyPath: 'id' });
                store.createIndex('updated_at', 'updated_at', { unique: false });
            }
        };

        request.onsuccess = event => {
            _db = event.target.result;
            resolve(_db);
        };

        request.onerror = event => reject(event.target.error);
    });
}

export const db = {
    async queueSale(saleData) {
        const db = await openDB();
        const tx = db.transaction('queued_sales', 'readwrite');
        tx.objectStore('queued_sales').add({
            ...saleData,
            status: 'pending',
            created_at: new Date().toISOString(),
        });
        return new Promise((resolve, reject) => {
            tx.oncomplete = resolve;
            tx.onerror = e => reject(e.target.error);
        });
    },

    async getQueuedSales() {
        const db = await openDB();
        const tx = db.transaction('queued_sales', 'readonly');
        const store = tx.objectStore('queued_sales');
        const index = store.index('status');
        const range = IDBKeyRange.only('pending');
        return new Promise((resolve, reject) => {
            const results = [];
            const cursor = index.openCursor(range);
            cursor.onsuccess = event => {
                const cur = event.target.result;
                if (cur) {
                    results.push({ id: cur.primaryKey, ...cur.value });
                    cur.continue();
                } else {
                    resolve(results);
                }
            };
            cursor.onerror = e => reject(e.target.error);
        });
    },

    async removeQueuedSale(id) {
        const db = await openDB();
        const tx = db.transaction('queued_sales', 'readwrite');
        tx.objectStore('queued_sales').delete(id);
        return new Promise((resolve, reject) => {
            tx.oncomplete = resolve;
            tx.onerror = e => reject(e.target.error);
        });
    },

    async cacheProducts(products) {
        const db = await openDB();
        const tx = db.transaction('cached_products', 'readwrite');
        const store = tx.objectStore('cached_products');
        const now = new Date().toISOString();
        products.forEach(p => store.put({ ...p, updated_at: now }));
        return new Promise((resolve, reject) => {
            tx.oncomplete = resolve;
            tx.onerror = e => reject(e.target.error);
        });
    },

    async getCachedProducts() {
        const db = await openDB();
        const tx = db.transaction('cached_products', 'readonly');
        const store = tx.objectStore('cached_products');
        return new Promise((resolve, reject) => {
            const results = [];
            const cursor = store.openCursor();
            cursor.onsuccess = event => {
                const cur = event.target.result;
                if (cur) {
                    results.push(cur.value);
                    cur.continue();
                } else {
                    resolve(results);
                }
            };
            cursor.onerror = e => reject(e.target.error);
        });
    },

    async searchCachedProducts(query) {
        const all = await this.getCachedProducts();
        const q = query.toLowerCase();
        return all.filter(p =>
            p.name.toLowerCase().includes(q) ||
            (p.sku && p.sku.toLowerCase().includes(q)) ||
            (p.barcode && p.barcode.toLowerCase().includes(q))
        );
    },

    async cacheCustomers(customers) {
        const db = await openDB();
        const tx = db.transaction('cached_customers', 'readwrite');
        const store = tx.objectStore('cached_customers');
        const now = new Date().toISOString();
        customers.forEach(c => store.put({ ...c, updated_at: now }));
        return new Promise((resolve, reject) => {
            tx.oncomplete = resolve;
            tx.onerror = e => reject(e.target.error);
        });
    },

    async getCachedCustomers() {
        const db = await openDB();
        const tx = db.transaction('cached_customers', 'readonly');
        const store = tx.objectStore('cached_customers');
        return new Promise((resolve, reject) => {
            const results = [];
            const cursor = store.openCursor();
            cursor.onsuccess = event => {
                const cur = event.target.result;
                if (cur) {
                    results.push(cur.value);
                    cur.continue();
                } else {
                    resolve(results);
                }
            };
            cursor.onerror = e => reject(e.target.error);
        });
    },

    async clearAll() {
        const db = await openDB();
        const stores = ['queued_sales', 'cached_products', 'cached_customers'];
        const tx = db.transaction(stores, 'readwrite');
        stores.forEach(s => tx.objectStore(s).clear());
        return new Promise((resolve, reject) => {
            tx.oncomplete = resolve;
            tx.onerror = e => reject(e.target.error);
        });
    },
};
