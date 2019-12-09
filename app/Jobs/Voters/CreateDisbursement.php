<?php

namespace App\Jobs\Voters;

use App\Models\Wallet;
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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The wallet instance.
     *
     * @var \App\Models\Wallet
     */
    public $wallet;

    /**
     * The nonce for the transaction.
     */
    public $nonce;

    /**
     * Create a new job instance.
     */
    public function __construct(Wallet $wallet, int $nonce)
    {
        $this->wallet = $wallet;
        $this->nonce = $nonce;
    }

    /**
     * Execute the job.
     *
     * @params \App\Services\Ark\Signer $signer
     */
    public function handle(Signer $signer)
    {
        $transfer = $signer->sign(
            $this->wallet->payout_address ? $this->wallet->payout_address : $this->wallet->address,
            $this->wallet->earnings,
            $this->nonce,
            config('delegate.vendorField')
        );

        if (!$transfer->verify()) {
            throw new RuntimeException('Invalid transaction: '.json_encode($transfer));
        }

        $transfer = transform_transfer($transfer->toArray());

        $this->wallet->disbursements()->create([
            'transaction_id' => $transfer['id'],
            'amount'         => $transfer['amount'],
            'purpose'        => $transfer['vendorField'],
            'signed_at'      => Carbon::now(),
            'transaction'    => $transfer,
        ]);

        $this->wallet->update(['earnings' => 0]);
    }
}
