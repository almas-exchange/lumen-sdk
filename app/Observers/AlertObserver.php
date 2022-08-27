<?php

namespace Exchange\Observers;

use App\Models\Alert;

class AlertObserver
{
    public function created(Alert $alert)
    {
        try {
            \Bschmitt\Amqp\Facades\Amqp::publish('',
                json_encode(['event' => 'alert', 'user' => $alert->user_id, 'data' => $alert->toArray()]),
                ['exchange' => 'exchange.websocket', 'exchange_type' => 'fanout']
            );
        } catch (\Throwable $throwable) {
            report($throwable);
        }
    }
}
