const CACHE = 'pos-cache-v1';

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE).then(cache =>
            cache.add('/').catch(() => {})
        )
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.filter(k => k !== CACHE).map(k => caches.delete(k))
        ))
    );
});

self.addEventListener('fetch', event => {
    if (event.request.url.startsWith(self.location.origin)) {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(event.request))
        );
    }
});
