/**
 * StockFlow — service worker.
 * Les pages ne sont jamais mises en cache (données privées) : elles passent toujours par le réseau,
 * avec une page « hors ligne » si la connexion tombe. Seuls les fichiers du thème sont mis en cache.
 */
const CACHE = 'stockflow-v1';
const BASE = self.registration.scope;
const FICHIERS = [
    'offline.html',
    'css/stockflow.css',
    'css/stockflow-auth.css',
    'js/stockflow.js',
    'images/stockflow-logo.svg',
    'icons/icon-192.png',
].map((chemin) => new URL(chemin, BASE).href);

self.addEventListener('install', (evenement) => {
    evenement.waitUntil(caches.open(CACHE).then((cache) => cache.addAll(FICHIERS)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (evenement) => {
    evenement.waitUntil(
        caches.keys()
            .then((cles) => Promise.all(cles.filter((cle) => cle !== CACHE).map((cle) => caches.delete(cle))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (evenement) => {
    const requete = evenement.request;
    if (requete.method !== 'GET') {
        return;
    }

    if (requete.mode === 'navigate') {
        evenement.respondWith(fetch(requete).catch(() => caches.match(new URL('offline.html', BASE).href)));
        return;
    }

    const url = new URL(requete.url);
    const estFichierTheme = url.origin === self.location.origin && /\/(css|js|images|icons)\//.test(url.pathname);
    if (!estFichierTheme) {
        return;
    }

    evenement.respondWith(
        caches.open(CACHE).then((cache) =>
            cache.match(requete).then((enCache) => {
                const reseau = fetch(requete)
                    .then((reponse) => {
                        if (reponse.ok) {
                            cache.put(requete, reponse.clone());
                        }
                        return reponse;
                    })
                    .catch(() => enCache);
                return enCache || reseau;
            })
        )
    );
});
