const CACHE_VERSION = "v3"; // Updated cache version - non-scrollable
const CACHE_NAME = "offline-" + CACHE_VERSION;

const preLoad = function () {
    return caches.open(CACHE_NAME).then(function (cache) {
        // caching index and important routes
        return cache.addAll(filesToCache);
    });
};

self.addEventListener("install", function (event) {
    event.waitUntil(preLoad());
    // Force the waiting service worker to become the active service worker
    self.skipWaiting();
});

// Clean up old caches
self.addEventListener("activate", function (event) {
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    // Ensure the new service worker takes control immediately
    return self.clients.claim();
});

const filesToCache = ["/", "/offline.html"];

const checkResponse = function (request) {
    return new Promise(function (fulfill, reject) {
        fetch(request).then(function (response) {
            if (response.status !== 404) {
                fulfill(response);
            } else {
                reject();
            }
        }, reject);
    });
};

const addToCache = function (request) {
    // Only cache http(s) requests
    if (!request.url.startsWith("http")) {
        return Promise.resolve();
    }
    return caches.open(CACHE_NAME).then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request, response);
        });
    });
};

const returnFromCache = function (request) {
    return caches.open(CACHE_NAME).then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                return cache.match("offline.html");
            } else {
                return matching;
            }
        });
    });
};

self.addEventListener("fetch", function (event) {
    event.respondWith(
        checkResponse(event.request).catch(function () {
            return returnFromCache(event.request);
        })
    );
    if (!event.request.url.startsWith("http")) {
        event.waitUntil(addToCache(event.request));
    }
});
