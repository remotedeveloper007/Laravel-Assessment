self.addEventListener("push", function(event) {
  const data = event.data ? event.data.json() : {};
  event.waitUntil(
    self.registration.showNotification(data.title || "New Notification", {
      body: data.body || "",
      data: data
    })
  );
});

self.addEventListener("notificationclick", function(event) {
  event.notification.close();
  // You can customize click behavior (open order page, etc.)
  event.waitUntil(
    clients.openWindow("/")
  );
});
