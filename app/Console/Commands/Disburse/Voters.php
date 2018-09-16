<?php

namespace App\Console\Commands\Disburse;

use App\Jobs\Voters\BroadcastDisbursement;
use App\Jobs\Voters\CreateDisbursement;
use App\Models\Wallet;
use Illuminate\Console\Command;

class Voters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:disburse:voters';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Wallet::public()->get()->each(function ($wallet) {
            if ($wallet->shouldBePaid()) {
                $this->line("Disbursing Wallet: <info>{$wallet->address}</info>");

                CreateDisbursement::withChain([
                    new BroadcastDisbursement($wallet),
                ])->dispatch($wallet)->allOnQueue('disbursements');
            }
        });
    }
}
