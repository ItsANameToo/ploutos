<?php

namespace App\Console\Commands;

use App\Jobs\BroadcastTransaction;
use App\Models\Transaction;
use App\Services\Ark\Broadcaster;
use Illuminate\Console\Command;

class BroadcastTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:broadcast {number=10}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Broadcaster $broadcaster)
    {
        $transactions = Transaction::latest()
            ->limit($this->argument('number'))
            ->get();

        $transactions->each(function ($transaction) {
            $this->line("Broadcasting Transaction: <info>{$transaction->transaction_id}</info>");

            BroadcastTransaction::dispatch($transaction)->allOnQueue('transactions');
        });
    }
}
