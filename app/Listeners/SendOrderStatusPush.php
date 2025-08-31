<?php

namespace App\Listeners;

use Minishlink\WebPush\WebPush;
use App\Models\PushSubscription;
use App\Events\OrderStatusUpdated;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\MessageSentReport;

class SendOrderStatusPush
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusUpdated $event): void
    {
        //
        $order = $event->order;
        $pushSubscription = PushSubscription::where('customer_id', $order->customer_id)->get();

        if ($pushSubscription->isEmpty()) return;

        $auth = [
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY'),
            ],
        ];

        $webPush = new WebPush($auth);

        foreach ($pushSubscription as $pushNotification) {
            $subscription = Subscription::create([
                'endpoint' => $pushNotification->endpoint,
                'publicKey' => $pushNotification->p256dh,
                'authToken' => $pushNotification->auth,
            ]);

            $payload = json_encode([
                'title' => 'Order #'.$order->id.' '.$order->status,
                'body' => 'Your order status is now '.$order->status,
                'order_id' => $order->id,
            ]);

            $webPush->queueNotification($subscription, $payload);
        }

        foreach ($webPush->flush() as $report) {
            /** @var MessageSentReport $report */
            if ($report->isSuccess()) {} else {
                $endpoint = $report->getRequest()->getUri()->__toString();
                PushSubscription::where('endpoint', $endpoint)->delete();
            }
        }
    }
}
