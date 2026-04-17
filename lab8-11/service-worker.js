var cacheName = 'hotelPWA-v2';
var filesToCache = [
    './',
    './index.php',
    './style.css',
    './js/daypilot-all.min.js',
    './manifest.json',
    './img/icon-192.png',
    './img/icon-512.png',
    'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'
    ];

    self.addEventListener('install', function(e) {
    console.log('[ServiceWorker] Install');
    e.waitUntil(
        caches.open(cacheName).then(function(cache) {
        return cache.addAll(filesToCache);
        })
    );
    });

    self.addEventListener('activate', function(e) {
    e.waitUntil(
        caches.keys().then(function(keyList) {
        return Promise.all(keyList.map(function(key) {
            if (key !== cacheName) {
            return caches.delete(key);
            }
        }));
        })
    );
    return self.clients.claim();
    });

    self.addEventListener('fetch', function(e) {
    if (e.request.method === 'POST') return;
    e.respondWith(
        caches.match(e.request).then(function(response) {
        return response || fetch(e.request);
        })
    );
});