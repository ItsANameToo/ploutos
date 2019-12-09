<?php

namespace App\Console\Commands\Disburse;

use App\Models\Disbursement;
use App\Services\Ark\Broadcaster;
use App\Services\Ark\Client;
use App\Services\Ark\Signer;
use ArkX\Calculus\BigNumber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class Developer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:disburse:developer';

    /**
     * Execute the console command.
     *
     * @param \App\Services\Ark\Signer      $broadcaster
     * @param \App\Services\Ark\Broadcaster $signer
     *
     * @return mixed
     */
    public function handle(Signer $signer, Broadcaster $broadcaster, Client $client)
    {
        $earnings = $this->earnings();
        $nonce = $client->nonce() + 1;

        if (config('delegate.fees.cover') && config('delegate.fees.deduct')) {
            $earnings -= $this->fee();
        }

        if ($earnings <= 0) {
            throw new \Exception('Insufficient funds for a delegate payout! Available funds: '.$earnings);
        }

        $transfer = $signer->sign(
            config('delegate.personal.address'),
            $earnings,
            $nonce,
            config('delegate.personal.vendorField')
        );

        if ($transfer->verify()) {
            config('delegate.broadcastType') === 'spread'
            ? $broadcaster->spread($transfer->toArray())
            : $broadcaster->broadcast($transfer->toArray());

            Cache::forever('delegate.earnings', 0);
        } else {
            $this->error('The signed transaction could not be verified.');
        }
    }

    public function earnings(): int
    {
        return BigNumber::create(cache('delegate.earnings'))->toInteger();
    }

    public function fee(): int
    {
        $count = Disbursement::sinceMidnight()->count();

        return (0.1 * ARKTOSHI) * $count;
    }
}
