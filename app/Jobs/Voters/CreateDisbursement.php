<?php

namespace App\Jobs\Voters;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Ark\Broadcaster;
use App\Services\Ark\Signer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class CreateDisbursement implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    /**
     * The wallet instances.
     *
     * @var \App\Models\Wallet
     */
    public $wallets;

    /**
     * The nonce for the transaction.
     */
    public $nonce;

    /**
     * Create a new job instance.
     */
    public function __construct(array $wallets, int $nonce)
    {
        $this->wallets = $wallets;
        $this->nonce = $nonce;
    }

    /**
     * Execute the job.
     *
     * @params \App\Services\Ark\Signer $signer
     */
    public function handle(Signer $signer, Broadcaster $broadcaster)
    {
        if (count($this->wallets) > 1) {
            $transfer = $signer->signMultipayment(
                $this->wallets,
                $this->nonce,
                config('delegate.vendorField')
            );
        } else {
            $wallet = $this->wallets[0];
            $transfer = $signer->sign(
                $wallet->payout_address ? $wallet->payout_address : $wallet->address,
                $wallet->earnings,
                $this->nonce,
                config('delegate.vendorField')
            );
        }

        if (!$transfer->verify()) {
            throw new RuntimeException('Invalid transaction: '.json_encode($transfer));
        }

        $transfer = $transfer->toArray();

        config('delegate.broadcastType') === 'spread'
            ? $broadcaster->spread($transfer)
            : $broadcaster->broadcast($transfer);

        // TODO: make an expiration thingy so they aren't 0 by default

        foreach ($this->wallets as $wallet) {
            $disbursement = $wallet->disbursements()->create([
                'transaction_id' => $transfer['id'],
                'amount'         => $wallet->earnings,
                'purpose'        => $transfer['vendorField'],
                'signed_at'      => Carbon::now(),
            ]);

            Transaction::updateOrCreate(['transaction_id' => $transfer['id']], [
                'transaction' => $transfer,
            ]);

            $wallet->update(['earnings' => 0]);
        }
    }
}
