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
        $walletsToBePaid = [];
        $wallets->get()->each(function ($wallet) use (&$walletsToBePaid) {
            if ($wallet->shouldBePaid()) {
                $this->line("Eligible for payment: <info>{$wallet->address}</info>");
                $walletsToBePaid[] = $wallet;
            }
        });

        if (count($walletsToBePaid) == 0) {
            return;
        }

        // Split eligible wallets based on max payouts per transaction
        $walletsSplit = array_chunk($walletsToBePaid, config('delegate.multipaymentCount'));
        foreach ($walletsSplit as $walletList) {
            CreateDisbursement::dispatch($walletList, $nonce)->allOnQueue('disbursements');
            $nonce += 1;
        }

        // withChain([
        //     new BroadcastDisbursement($wallet),
        // ])->
        // CreateDisbursement::dispatch($walletsToBePaid, $nonce)->allOnQueue('disbursements');
    }
}
