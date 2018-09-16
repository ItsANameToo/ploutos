<?php

namespace App\Console\Commands\Numbers;

use App\Models\Wallet;
use App\Services\Calculator;
use Illuminate\Console\Command;

class EstimateEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:estimate';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Calculator $calculator)
    {
        $wallets = Wallet::public()->get()->map(function ($wallet) {
            $wallet->earnings = 0;

            return $wallet;
        });

        for ($i = 0; $i < 211; $i++) {
            $wallets->each(function ($wallet) use ($calculator) {
                $wallet->earnings += $calculator->perBlock($wallet->stake)->toInteger();
            });
        }

        $wallets = $wallets->map(function ($wallet) {
            return [
                'role'     => 'Voter',
                'address'  => $wallet->address,
                'balance'  => $wallet->stake / ARKTOSHI,
                'earnings' => $wallet->earnings / ARKTOSHI,
            ];
        })->sortByDesc('balance');

        if (env('ARK_DELEGATE_PERSONAL_ADDRESS')) {
            $sharePercentage = env('ARK_DELEGATE_PERSONAL_SHARE_PERCENTAGE');

            $wallets->push([
                'role'     => 'Delegate',
                'address'  => env('ARK_DELEGATE_PERSONAL_ADDRESS'),
                'balance'  => 0,
                'earnings' => 422 * ($sharePercentage / 100),
            ]);
        }

        $this->table(['Role', 'Address', 'Stake', 'Earnings'], $wallets);
    }
}
