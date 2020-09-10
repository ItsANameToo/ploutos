<?php

namespace App\Console\Commands\Polling;

use App\Models\Wallet;
use App\Services\Ark\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Voters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:poll:voters';

    /**
     * Execute the console command.
     */
    public function handle(Client $client): void
    {
        foreach ($client->voters() as $wallet) {
            $this->line('Polling Wallet: <info>'.$wallet['address'].'</info>');

            try {
                Wallet::findByAddress($wallet['address'])->update([
                    'balance' => $this->isCompendia() ? $wallet['power'] : $wallet['balance'],
                ]);
            } catch (\Exception $e) {
                Wallet::create([
                    'address'    => $wallet['address'],
                    'public_key' => $wallet['publicKey'],
                    'balance'    => $this->isCompendia() ? $wallet['power'] : $wallet['balance'],
                ]);
            }
        }
    }

    private function isCompendia(): bool
    {
        return Str::contains(config('delegate.network'), 'compendia');
    }
}
