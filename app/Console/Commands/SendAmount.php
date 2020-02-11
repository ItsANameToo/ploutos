<?php

namespace App\Console\Commands;

use App\Services\Ark\Broadcaster;
use App\Services\Ark\Client;
use App\Services\Ark\Signer;
use Illuminate\Console\Command;

class SendAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:send {amount} {recipient} {--smartbridge=}';

    /**
     * Execute the console command.
     *
     * @param \App\Services\Ark\Signer      $broadcaster
     * @param \App\Services\Ark\Broadcaster $signer
     */
    public function handle(Signer $signer, Broadcaster $broadcaster, Client $client): void
    {
        $smartbridge = is_null($this->option('smartbridge')) ? '' : $this->option('smartbridge');
        $nonce = $client->nonce() + 1;
        $transfer = $signer->sign(
            $this->argument('recipient'),
            $this->argument('amount') * ARKTOSHI,
            $nonce,
            $smartbridge
        );

        if ($transfer->verify()) {
            config('delegate.broadcastType') === 'spread'
            ? $broadcaster->spread($transfer->toArray())
            : $broadcaster->broadcast($transfer->toArray());
        } else {
            $this->error('The signed transaction could not be verified.');
        }
    }
}
