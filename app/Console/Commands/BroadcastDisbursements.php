<?php

namespace App\Console\Commands;

use App\Jobs\BroadcastDisbursement;
use App\Models\Disbursement;
use App\Services\Ark\Broadcaster;
use Illuminate\Console\Command;

class BroadcastDisbursements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:broadcast {number=100}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Broadcaster $broadcaster)
    {
        $disbursements = Disbursement::latest()
            ->limit($this->argument('number'))
            ->get();

        $disbursements->each(function ($disbursement) {
            $this->line("Broadcasting Transaction: <info>{$disbursement->transaction_id}</info>");

            BroadcastDisbursement::dispatch($disbursement)->allOnQueue('disbursements');
        });
    }
}
