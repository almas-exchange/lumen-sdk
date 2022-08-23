<?php

namespace Exchange\Observers;

use App\Models\Notification;

class NotificationObserver
{
    public function created(Notification $notification)
    {
        try {
            \Bschmitt\Amqp\Facades\Amqp::publish('',
                json_encode($notification),
                ['exchange' => 'exchange.notification', 'exchange_type' => 'fanout']
            );
        } catch (\Throwable $throwable) {
            report($throwable);
        }
    }
}
