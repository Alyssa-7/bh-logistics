self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', e => e.waitUntil(self.clients.claim()));

self.addEventListener('push', event => {
    let data = { title: 'BH-Logistic', body: 'มีการแจ้งเตือนใหม่', url: '/' };

    if (event.data) {
        try { data = { ...data, ...event.data.json() }; }
        catch { data.body = event.data.text() || data.body; }
    }

    const options = {
        body: data.body,
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        data: { url: data.url || '/' },
        requireInteraction: false,
        vibrate: [200, 100, 200],
    };

    event.waitUntil(Promise.all([
        self.registration.showNotification(data.title, options),
        // บอกทุก tab ที่เปิดอยู่ให้ reload notification bell ทันที
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clients => {
            clients.forEach(client => client.postMessage({ type: 'RELOAD_NOTIFICATIONS' }));
        }),
    ]));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            for (const client of list) {
                if ('focus' in client) return client.focus();
            }
            return clients.openWindow(event.notification.data?.url || '/');
        })
    );
});
