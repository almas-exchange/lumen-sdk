<?php

namespace Exchange\Jobs;

use App\Jobs\Job;
use Illuminate\Bus\Queueable;

class TransactionJob extends Job
{
    use Queueable;

    private $data;

    public function __construct($data)
    {
        $this->onQueue('mx_transaction');
        $this->data = $data;
    }

    public function handle()
    {
        try {
            // Implementation is in transaction-lumen
        } catch (\Throwable $throwable) {
            report($throwable);
        }
    }
}
