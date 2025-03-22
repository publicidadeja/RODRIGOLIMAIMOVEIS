const CACHE_NAME = 'imobiliaria-cache-v1';
const urlsToCache = [
  '/',
  '/css/style.css',
  '/js/app.js',
  '/images/logo.png',
  // Adicione outros recursos importantes aqui
];

// Instalação do Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache aberto');
        return cache.addAll(urlsToCache);
      })
  );
});

// Estratégia de cache: Network first, falling back to cache
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request)
      .catch(() => {
        return caches.match(event.request);
      })
  );
});