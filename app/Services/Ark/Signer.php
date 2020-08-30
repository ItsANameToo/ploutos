<?php

namespace App\Services\Ark;

use ArkEcosystem\Crypto\Transactions\Builder\MultiPaymentBuilder;
use ArkEcosystem\Crypto\Transactions\Builder\TransferBuilder;

class Signer
{
    /**
     * Sign a transfer transaction.
     *
     * @param string $recipient
     * @param int    $amount
     * @param string $purpose
     *
     * @return \ArkEcosystem\Crypto\Transactions\Builder\Transfer
     */
    public function sign(string $recipient, int $amount, int $nonce, string $purpose): TransferBuilder
    {
        set_crypto_network();

        return TransferBuilder::new()
            ->recipient($recipient)
            ->amount($amount)
            ->vendorField($purpose)
            ->withNonce($nonce)
            ->withFee(config('delegate.fees.transfer'))
            ->sign(decrypt(config('delegate.passphrase')))
            ->secondSign(decrypt(config('delegate.secondPassphrase')));
    }

    public function signMultipayment(array $wallets, int $nonce, string $purpose): MultipaymentBuilder
    {
        set_crypto_network();

        $multipayment = MultipaymentBuilder::new()
            ->vendorField($purpose)
            ->withFee(config('delegate.fees.multipayment'))
            ->withNonce($nonce);

        foreach ($wallets as $wallet) {
            $multipayment->add(($wallet->payout_address ? $wallet->payout_address : $wallet->address), $wallet->earnings);
        }

        return $multipayment
            ->sign(decrypt(config('delegate.passphrase')))
            ->secondSign(decrypt(config('delegate.secondPassphrase')));
    }
}
