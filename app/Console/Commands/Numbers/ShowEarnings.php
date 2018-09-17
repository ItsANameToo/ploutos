<?php

namespace App\Console\Commands\Numbers;

use App\Models\Wallet;
use Illuminate\Console\Command;

class ShowEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:earnings {--banned}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $voterSum = 0;
        $payoutFees = 0;

        $wallets = $this->option('banned') ? Wallet::notBlacklisted() : Wallet::public();
        $wallets = $wallets->get(['address', 'balance', 'earnings', 'banned_at'])
            ->map(function ($wallet) use (&$voterSum, &$payoutFees) {
                $voterSum += $wallet->earnings / ARKTOSHI;

                if (config('delegate.fees.cover') && $wallet->earnings / ARKTOSHI >= config('delegate.threshold')) {
                    $payoutFees += 0.1;
                }

                return [
                    'role'     => 'Voter',
                    'address'  => $wallet->address,
                    'balance'  => $wallet->balance / ARKTOSHI,
                    'earnings' => $wallet->earnings / ARKTOSHI,
                    'banned'   => $wallet->banned_at ? 'Yes' : 'No',
                ];
            });

        if (config('delegate.personal.address')) {
            $wallets->push([
                'role'      => 'Delegate',
                'address'   => config('delegate.personal.address'),
                'balance'   => 0,
                'earnings'  => cache('delegate.earnings') / ARKTOSHI,
                'banned_at' => 'No',
            ]);
        }

        $this->table(['Role', 'Address', 'Stake', 'Earnings', 'Banned'], $wallets);
        $this->line("Paying out to voters in total: <info>{$voterSum}</info>");

        if (config('delegate.fees.cover')) {
            $this->line("Total fees covered by delegate: <info>{$payoutFees}</info>");
        }
    }
}
