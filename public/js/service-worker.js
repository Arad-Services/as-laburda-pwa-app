/**
 * Service Worker for AS Laburda PWA App.
 *
 * This script defines the caching strategies for the PWA, enabling offline access
 * and faster loading times. It's served from the root scope ('/service-worker.js').
 *
 * @link       https://arad-services.com
 * @since      2.0.0
 * @package    AS_Laburda_PWA_App
 * @subpackage AS_Laburda_PWA_App/public/js
 */

// Define a cache name. Incrementing this will trigger a service worker update.
const CACHE_NAME = 'aslp-pwa-cache-v1.0.0';

// List of URLs to cache on install (precaching).
// This should include your essential static assets.
const urlsToCache = [
    '/', // Your main site URL
    '/index.php', // WordPress front page
    // Placeholder paths for common plugin assets (adjust as needed once final structure is known)
    // For example:
    // '/wp-content/plugins/as-laburda-pwa-app/public/css/as-laburda-pwa-app-public.css',
    // '/wp-content/plugins/as-laburda-pwa-app/public/js/as-laburda-pwa-app-public.js',
    // '/wp-content/plugins/as-laburda-pwa-app/public/images/icon-192x192.png',
    // '/wp-content/plugins/as-laburda-pwa-app/public/images/icon-512x512.png',
    // You might also cache a dedicated offline page
    // '/offline.html' // If you create a simple static offline page
];

// Cache all assets received from the server if they are a successful response.
// This is typically called "Cache-First" or "Cache, then Network" strategy.
self.addEventListener('install', function(event) {
    console.log('[ServiceWorker] Install');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('[ServiceWorker] Caching app shell');
                // Use cache.addAll for precaching if you have a predefined list of assets.
                // For dynamic content, a runtime caching strategy (fetch listener) is better.
                return cache.addAll(urlsToCache).catch(function(error) {
                    console.error('[ServiceWorker] Failed to cache some URLs:', error);
                });
            })
    );
});

self.addEventListener('activate', function(event) {
    console.log('[ServiceWorker] Activate');
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName.startsWith('aslp-pwa-cache-') && cacheName !== CACHE_NAME) {
                        console.log('[ServiceWorker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(function() {
            // Clients.claim() is important to allow the activated service worker to take control
            // of the page immediately, rather than on the next navigation or refresh.
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function(event) {
    // Only cache GET requests, and exclude chrome-extension, etc.
    if (event.request.method === 'GET' && !event.request.url.startsWith('chrome-extension://')) {
        event.respondWith(
            caches.match(event.request).then(function(response) {
                // Cache hit - return response
                if (response) {
                    return response;
                }

                // IMPORTANT: Clone the request. A request is a stream and
                // can only be consumed once. Since we are consuming this
                // once by cache and once by the browser for fetch, we need
                // to clone the request.
                var fetchRequest = event.request.clone();

                return fetch(fetchRequest).then(
                    function(response) {
                        // Check if we received a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // IMPORTANT: Clone the response. A response is a stream and
                        // can only be consumed once. Since we are consuming this
                        // once by cache and once by the browser for return, we need
                        // to clone the response.
                        var responseToCache = response.clone();

                        // Open the cache and put the new response in it
                        caches.open(CACHE_NAME)
                            .then(function(cache) {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    }
                ).catch(function(error) {
                    console.error('[ServiceWorker] Fetch failed:', event.request.url, error);
                    // This catch is for when network fails.
                    // You can return a fallback response here, e.g., an offline page.
                    // For example:
                    // return caches.match('/offline.html');
                });
            })
        );
    }
});

// Optional: Handle push notifications (if implemented in your PWA)
self.addEventListener('push', function(event) {
    const data = event.data.json();
    console.log('[ServiceWorker] Push Received:', data);

    const title = data.title || 'AS Laburda PWA App';
    const options = {
        body: data.body || 'You have a new notification!',
        icon: data.icon || '/wp-content/plugins/as-laburda-pwa-app/public/images/icon-192x192.png',
        badge: data.badge || '/wp-content/plugins/as-laburda-pwa-app/public/images/icon-96x96.png', // smaller icon
        image: data.image || undefined,
        data: {
            url: data.url || '/' // URL to open when notification is clicked
        }
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
    console.log('[ServiceWorker] Notification click Received.');
    event.notification.close();

    // This looks for a window that is already open and focuses it.
    // If not, it opens a new window to the provided URL.
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            if (event.notification.data.url) {
                for (var i = 0; i < clientList.length; i++) {
                    var client = clientList[i];
                    if (client.url.includes(event.notification.data.url) && 'focus' in client) {
                        return client.focus();
                    }
                }
                return clients.openWindow(event.notification.data.url);
            }
        })
    );
});
