<?php

namespace App\Jobs;

use App\Models\Disbursement;
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
     * The disbursement instance.
     *
     * @var \App\Models\Disbursement
     */
    public $disbursement;

    /**
     * Create a new job instance.
     */
    public function __construct(Disbursement $disbursement)
    {
        $this->disbursement = $disbursement;
    }

    /**
     * Execute the job.
     *
     * @params \App\Services\Ark\Broadcaster $broadcaster
     */
    public function handle(Broadcaster $broadcaster)
    {
        $transaction = $this->disbursement->transaction;

        config('delegate.broadcastType') === 'spread'
            ? $broadcaster->spread($transaction)
            : $broadcaster->broadcast($transaction);
    }
}
