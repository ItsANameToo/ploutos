<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\Ark\Broadcaster;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The transaction instance.
     *
     * @var \App\Models\Transaction
     */
    public $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @params \App\Services\Ark\Broadcaster $broadcaster
     */
    public function handle(Broadcaster $broadcaster)
    {
        $transaction = $this->transaction->transaction;

        config('delegate.broadcastType') === 'spread'
            ? $broadcaster->spread($transaction)
            : $broadcaster->broadcast($transaction);
    }
}
