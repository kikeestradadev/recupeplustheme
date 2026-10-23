const CACHE = 'recupeplus-pwa-v2';
const THEME_BASE = themeBaseFrom(self.location.href);
const themeFile = (path) => (THEME_BASE ? new URL(path, THEME_BASE).href : '');
const OFFLINE_URL = themeFile('pwa/offline.html');

self.addEventListener('install', (event) => {
	const urls = [
		OFFLINE_URL,
		themeFile('pwa/icons/icon-192.png'),
		themeFile('pwa/icons/icon-512.png'),
		themeFile('images/favicon.png'),
	].filter(Boolean);

	event.waitUntil(
		(urls.length ? caches.open(CACHE).then((cache) => cache.addAll(urls)) : Promise.resolve()).then(() =>
			self.skipWaiting()
		)
	);
});

self.addEventListener('activate', (event) => {
	event.waitUntil(
		caches
			.keys()
			.then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))))
			.then(() => self.clients.claim())
	);
});

self.addEventListener('fetch', (event) => {
	const request = event.request;
	if (request.method !== 'GET') {
		return;
	}

	const url = new URL(request.url);
	if (url.origin !== self.location.origin || shouldBypass(url)) {
		return;
	}

	if (request.mode === 'navigate') {
		event.respondWith(
			fetch(request)
				.then((response) => {
					if (response && response.ok) {
						const copy = response.clone();
						caches.open(CACHE).then((cache) => cache.put(request, copy));
					}
					return response;
				})
				.catch(async () => (await caches.match(request)) || (OFFLINE_URL ? caches.match(OFFLINE_URL) : undefined))
		);
		return;
	}

	if (!isCacheableAsset(url.pathname, THEME_BASE)) {
		return;
	}

	event.respondWith(
		caches.match(request).then((cached) => {
			const network = fetch(request)
				.then((response) => {
					if (response && response.ok) {
						const copy = response.clone();
						caches.open(CACHE).then((cache) => cache.put(request, copy));
					}
					return response;
				})
				.catch(() => cached);
			return cached || network;
		})
	);
});
