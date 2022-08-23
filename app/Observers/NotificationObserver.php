<?php

namespace Exchange\Observers;

use App\Models\Notification;

class NotificationObserver
{
    public function created(Notification $notification)
    {
        try {
            \Bschmitt\Amqp\Facades\Amqp::publish('route.notification',
                json_encode($notification),
                ['exchange' => 'exchange.notification', 'exchange_type' => 'direct']
            );
        } catch (\Throwable $throwable) {
            report($throwable);
        }
    }
}
