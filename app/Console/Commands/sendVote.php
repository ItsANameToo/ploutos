<?php

namespace App\Console\Commands;

use App\Services\Ark\Broadcaster;
use App\Services\Ark\Signer;
use Illuminate\Console\Command;

class sendVote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:vote {delegate}';

    /**
     * Execute the console command.
     *
     * @param \App\Services\Ark\Signer      $broadcaster
     * @param \App\Services\Ark\Broadcaster $signer
     */
    public function handle(Signer $signer, Broadcaster $broadcaster): void
    {
        $vote = $signer->signVote(
            $this->argument('delegate')
        );
        echo $vote;

        if (config('delegate.mode') !== 'dummy') {
            if ($vote->verify()) {
                config('delegate.broadcastType') === 'spread'
                ? $broadcaster->spread($vote->toArray())
                : $broadcaster->broadcast($vote->toArray());
            } else {
                $this->error('The signed transaction could not be verified.');
            }
        }
    }
}
