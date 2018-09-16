<?php

namespace App\Console\Commands\Distribute;

use App\Models\Wallet;
use App\Services\Calculator;
use Illuminate\Console\Command;

class Amount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:distribute:amount {amount}';

    /**
     * Execute the console command.
     */
    public function handle(Calculator $calculator): void
    {
        $amount = $this->argument('amount') * ARKTOSHI;

        Wallet::public()->each(function ($wallet) use ($calculator, $amount) {
            $this->line("Processing Wallet: <info>{$wallet->address}</info>");

            $calculator->setReward($amount);

            $earnings = $calculator->perBlock($wallet->stake)->toInteger();

            $wallet->increment('earnings', $earnings);
        });
    }
}
