<?php

namespace App\Jobs\Voters;

use App\Models\Wallet;
use App\Services\Ark\Broadcaster;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastDisbursement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The wallet instance.
     *
     * @var \App\Models\Wallet
     */
    public $wallet;

    /**
     * Create a new job instance.
     */
    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * Execute the job.
     *
     * @params \App\Services\Ark\Broadcaster $broadcaster
     */
    public function handle(Broadcaster $broadcaster)
    {
        $transaction = $this->wallet->latestDisbursement()->transaction;

        config('delegate.broadcastType') === 'spread'
            ? $broadcaster->spread($transaction)
            : $broadcaster->broadcast($transaction);
    }
}
