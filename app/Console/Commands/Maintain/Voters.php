<?php

namespace App\Console\Commands\Maintain;

use App\Models\Wallet;
use App\Services\Ark\Client;
use App\Services\Warden;
use Illuminate\Console\Command;

class Voters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:maintain:voters';

    /**
     * Execute the console command.
     */
    public function handle(Client $client): void
    {
        $voters = collect($client->voters())->pluck('address');

        if (empty($voters)) {
            return;
        }

        $wallets = Wallet::get();
        $wallets->each->unban();

        $wallets->each(function ($wallet) use ($voters) {
            $this->line('Polling Wallet: <info>'.$wallet['address'].'</info>');

            if (Warden::whitelisted($wallet['address'])) {
                return $wallet->unban();
            }

            if (Warden::blacklisted($wallet['address'])) {
                return $wallet->ban();
            }

            if (!$voters->contains($wallet['address'])) {
                return $wallet->ban();
            }
        });
    }
}
