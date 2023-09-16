<?php

namespace NadzorServera\Skijasi\Helpers\Firebase;

use Illuminate\Support\Facades\File;

class FirebasePublishFile
{
    public function getContentFirebaseMessagingSwJs()
    {
        $FIREBASE_API_KEY = \env('FIREBASE_API_KEY');
        $FIREBASE_AUTH_DOMAIN = \env('FIREBASE_AUTH_DOMAIN');
        $FIREBASE_PROJECT_ID = \env('FIREBASE_PROJECT_ID');
        $FIREBASE_STORAGE_BUCKET = \env('FIREBASE_STORAGE_BUCKET');
        $FIREBASE_MESSAGE_SEENDER = \env('FIREBASE_MESSAGE_SEENDER');
        $FIREBASE_APP_ID = \env('FIREBASE_APP_ID');
        $FIREBASE_MEASUREMENT_ID = \env('FIREBASE_MEASUREMENT_ID');

        $script_content = <<<JAVASCRIPT
        let cacheName = "app-skijasi-cache";
        let broadcastChannelName = "sw-skijasi-messages";
        const BROADCAST_TYPE_ONLINE_STATUS = "BROADCAST_TYPE_ONLINE_STATUS";
        const BROADCAST_TYPE_FIREBASE_MESSAGE = "BROADCAST_TYPE_FIREBASE_MESSAGE";
        let broadcastChannel = null;
        let broadcastMessageFormat = (type, data, message, errors) => {};

        try {
            let broadcastChannel = new BroadcastChannel(broadcastChannelName);
            let broadcastMessageFormat = (type, data, message, errors) => {
            broadcastChannel.postMessage({ type, data, message, errors });
            };
        } catch (error) {
            console.log('Error broadcast channel ', error)
        }

        try {
        self.addEventListener("install", (e) => {
            console.log("Worker Installed");
        });
        self.addEventListener("active", (e) => {
            e.waitUntil(self.clients.claim());
            e.waitUntil(
            caches.keys().then((cacheNames) =>
                Promise.all(
                cacheNames.map((cache, index) => {
                    if (cache !== cacheName) {
                    caches.delete(cache);
                    }
                })
                )
            )
            );
        });
        self.addEventListener("fetch", function (event) {
            event.respondWith(
            caches.open(cacheName).then(function (cache) {
                return fetch(event.request)
                .then((networkResponse) => {
                    if (event.request.method == "GET") {
                    cache.put(event.request, networkResponse.clone());
                    }
                    return networkResponse;
                })
                .catch((error) => {
                    return caches.match(event.request).then((response) => {
                    return response;
                    });
                });
            })
            );
        });
        } catch (error) {}
        try {
            importScripts("https://www.gstatic.com/firebasejs/8.2.7/firebase-app.js");
            importScripts(
                "https://www.gstatic.com/firebasejs/8.2.7/firebase-messaging.js"
            );
            var firebaseConfig = {
                apiKey: "$FIREBASE_API_KEY",
                authDomain: "$FIREBASE_AUTH_DOMAIN",
                projectId: "$FIREBASE_PROJECT_ID",
                storageBucket: "$FIREBASE_STORAGE_BUCKET",
                messagingSenderId: "$FIREBASE_MESSAGE_SEENDER",
                appId: "$FIREBASE_APP_ID",
                measurementId: "$FIREBASE_MEASUREMENT_ID",
            };
            const app = firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging();
            messaging.onBackgroundMessage((payload) => {
                try {
                    broadcastMessageFormat(
                        BROADCAST_TYPE_FIREBASE_MESSAGE,
                        payload,
                        null,
                        null
                    );
                } catch (error) {
                    console.log('Error broadcast channel', error)
                }
            });
        } catch (error) {}
        JAVASCRIPT;

        return $script_content;
    }

    public static function publishNow()
    {
        $firebase_publish_file = new self();
        $path = public_path().'/firebase-messaging-sw.js';
        if (File::exists($path)) {
            File::delete($path);
        }

        File::put($path, $firebase_publish_file->getContentFirebaseMessagingSwJs());
    }
}
