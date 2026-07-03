<?php

namespace App\Services;

use App\Jobs\SendPushNotificationJob;

class PushNotificationService
{
    public function sendToUsers(array $userIds, string $title, string $body, string $url = '/'): void
    {
        if (empty($userIds) || blank(config('webpush.vapid.public_key'))) return;

        SendPushNotificationJob::dispatch($userIds, $title, $body, $url);
    }
}
