/**
 * Ayobuatbaik PWA Service Worker
 * - Kitab pages: Cache-First (Available Offline)
 * - Other pages: Network-First with Offline Fallback
 */

const CACHE_VERSION = 'ayobuatbaik-v2';
const OFFLINE_URL = '/offline';

// Files to pre-cache on install
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/icon ABBI.png',
    '/img/icon_ABBI.png',
];

// ==========================================
// INSTALL: Pre-cache essential assets
// ==========================================
self.addEventListener('install', (event) => {
    self.skipWaiting();
    
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => {
            console.log('[SW] Pre-caching offline assets');
            return cache.addAll(PRECACHE_ASSETS);
        })
    );
});

// ==========================================
// ACTIVATE: Clean up old caches
// ==========================================
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_VERSION)
                    .map((name) => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => self.clients.claim())
    );
});

// ==========================================
// FETCH: Smart caching strategies
// ==========================================
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Only handle GET requests
    if (request.method !== 'GET') return;

    // Skip external requests (CDNs, analytics, etc.)
    if (url.origin !== location.origin) return;

    // -----------------------------------------
    // STRATEGY 1: KITAB PAGES (Cache-First)
    // Jika sudah pernah dibuka, ambil dari cache.
    // Jika belum ada di cache, ambil dari network lalu simpan.
    // -----------------------------------------
    if (url.pathname.startsWith('/kitab')) {
        event.respondWith(
            caches.open(CACHE_VERSION).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        // Ada di cache, pakai itu
                        console.log('[SW] Kitab from cache:', url.pathname);
                        
                        // Background update (Stale-While-Revalidate)
                        fetch(request).then((networkResponse) => {
                            if (networkResponse.ok) {
                                cache.put(request, networkResponse.clone());
                            }
                        }).catch(() => {});
                        
                        return cachedResponse;
                    }

                    // Tidak ada di cache, ambil dari network
                    return fetch(request).then((networkResponse) => {
                        if (networkResponse.ok) {
                            console.log('[SW] Caching Kitab page:', url.pathname);
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => {
                        // Network gagal dan tidak ada cache
                        return caches.match(OFFLINE_URL);
                    });
                });
            })
        );
        return;
    }

    // -----------------------------------------
    // STRATEGY 2: STATIC ASSETS (Cache-First)
    // CSS, JS, Images
    // -----------------------------------------
    if (
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.jpeg') ||
        url.pathname.endsWith('.svg') ||
        url.pathname.endsWith('.woff2')
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                return cachedResponse || fetch(request).then((networkResponse) => {
                    // Cache static assets for future
                    if (networkResponse.ok) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_VERSION).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                });
            })
        );
        return;
    }

    // -----------------------------------------
    // STRATEGY 3: OTHER PAGES (Network-First)
    // Coba network dulu, kalau gagal tampilkan offline page.
    // -----------------------------------------
    event.respondWith(
        fetch(request)
            .then((response) => {
                return response;
            })
            .catch(() => {
                console.log('[SW] Network failed, showing offline page');
                return caches.match(OFFLINE_URL);
            })
    );
});