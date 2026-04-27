self.addEventListener('install', (event) => {
    console.log('Service Worker Kos Sunduwan: Berhasil Dipasang! 🚀');
    self.skipWaiting();
});

self.addEventListener('fetch', (event) => {
    // Logika minimal: Ambil data dari internet
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});