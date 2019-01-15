<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;

class SetPayoutAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:voter:payoutAddress {address} {payoutAddress?}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $wallet = Wallet::findByAddress($this->argument('address'));
        $payoutAddress = $this->argument('payoutAddress');
        if ($payoutAddress) {
            $wallet->update([
                'payout_address' => $payoutAddress,
            ]);
        } else {
            $wallet->update([
                'payout_address' => null,
            ]);
        }
    }
}
