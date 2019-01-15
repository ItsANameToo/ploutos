<?php

namespace App\Jobs;

use App\Models\Block;
use App\Models\Wallet;
use App\Services\Calculator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBlock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The block instance.
     *
     * @var \App\Models\Block
     */
    public $block;

    /**
     * Create a new job instance.
     */
    public function __construct(Block $block)
    {
        $this->block = $block;
    }

    /**
     * Execute the job.
     */
    public function handle(Calculator $calculator)
    {
        Wallet::public()->each(function ($wallet) use ($calculator) {
            $calculator->setReward($this->block->total);
            is_null($wallet->payout_perc) ? $calculator->setProfitShare(config('delegate.sharePercentage')): $calculator->setProfitShare($wallet->payout_perc);

            $earnings = $calculator->perBlock($wallet->stake)->toInteger();

            $wallet->increment('earnings', $earnings);
        });

        calculate_delegate_share($this->block);

        $this->block->markAsProcessed();
    }
}
