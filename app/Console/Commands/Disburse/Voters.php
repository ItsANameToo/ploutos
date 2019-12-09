<?php

namespace App\Console\Commands\Disburse;

use App\Jobs\Voters\BroadcastDisbursement;
use App\Jobs\Voters\CreateDisbursement;
use App\Models\Wallet;
use App\Services\Ark\Client;
use Illuminate\Console\Command;

class Voters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:disburse:voters {--banned}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Client $client)
    {
        $wallets = $this->option('banned') ? Wallet::notBlacklisted() : Wallet::public();

        $nonce = $client->nonce() + 1;
        $wallets->get()->each(function ($wallet) use (&$nonce) {
            if ($wallet->shouldBePaid()) {
                $this->line("Disbursing Wallet: <info>{$wallet->address}</info>");

                $this->line($nonce);
                CreateDisbursement::withChain([
                    new BroadcastDisbursement($wallet),
                ])->dispatch($wallet, $nonce)->allOnQueue('disbursements');

                $nonce += 1;
            }
        });
    }
}
