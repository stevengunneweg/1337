// /**
//  * Welcome to your Workbox-powered service worker!
//  *
//  * You'll need to register this file in your web app and you should
//  * disable HTTP caching for this file too.
//  * See https://goo.gl/nhQhGp
//  *
//  * The rest of the code is auto-generated. Please don't update this file
//  * directly; instead, make changes to your Workbox build configuration
//  * and re-run your build process.
//  * See https://goo.gl/2aRDsh
//  */

// importScripts('https://storage.googleapis.com/workbox-cdn/releases/4.3.1/workbox-sw.js');

// importScripts('/v2/precache-manifest.a085e8f64ee34e8303188fa612714eed.js');

// workbox.core.setCacheNameDetails({ prefix: '1337online' });

// self.addEventListener('message', (event) => {
// 	if (event.data && event.data.type === 'SKIP_WAITING') {
// 		self.skipWaiting();
// 	}
// });

// /**
//  * The workboxSW.precacheAndRoute() method efficiently caches and responds to
//  * requests for URLs in the manifest.
//  * See https://goo.gl/S9QRab
//  */
// self.__precacheManifest = [].concat(self.__precacheManifest || []);
// workbox.precaching.precacheAndRoute(self.__precacheManifest, {});

/**
 * Self-Destroy service worker
 */

self.addEventListener('install', function () {
	self.skipWaiting();
});

self.addEventListener('activate', function () {
	self.registration
		.unregister()
		// .then(function () {
		// 	return self.clients.matchAll();
		// })
		.then(function (clients) {
			clients.forEach((client) => client.navigate('https://1337online.com/'));
		});
});
