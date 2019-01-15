<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;

class SetVoterPayoutPercentage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:voter:percentage {address} {percentage?} ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $wallet = Wallet::findByAddress($this->argument('address'));
        $percentage = $this->argument('percentage');
        if ($percentage) {
            $wallet->update([
                'payout_perc' => $percentage,
            ]);
        } else {
            $wallet->update([
                'payout_perc' => null,
            ]);
        }
    }
}
