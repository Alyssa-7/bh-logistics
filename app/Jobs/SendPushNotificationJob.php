<?php

namespace App\Jobs;

use App\Models\PushSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 2;
    public int $timeout = 30;

    public function __construct(
        private array $userIds,
        private string $title,
        private string $body,
        private string $url = '/',
    ) {}

    public function handle(): void
    {
        if (empty($this->userIds) || blank(config('webpush.vapid.public_key'))) return;

        $subscriptions = PushSubscription::whereIn('user_id', $this->userIds)->get();
        if ($subscriptions->isEmpty()) return;

        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('webpush.vapid.subject'),
                'publicKey'  => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ]);
        $webPush->setReuseVAPIDHeaders(true);
        $webPush->setAutomaticPadding(false);

        $payload = json_encode(['title' => $this->title, 'body' => $this->body, 'url' => $this->url]);

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint'        => $sub->endpoint,
                    'contentEncoding' => 'aes128gcm',
                    'keys'            => ['p256dh' => $sub->p256dh_key, 'auth' => $sub->auth_token],
                ]),
                $payload
            );
        }

        $expiredEndpoints = [];
        foreach ($webPush->flush() as $report) {
            if ($report->isSubscriptionExpired()) {
                $expiredEndpoints[] = $report->getEndpoint();
            } elseif (!$report->isSuccess()) {
                Log::warning('Push failed: ' . $report->getReason(), ['endpoint' => $report->getEndpoint()]);
            }
        }

        if ($expiredEndpoints) {
            PushSubscription::whereIn('endpoint', $expiredEndpoints)->delete();
        }
    }
}
