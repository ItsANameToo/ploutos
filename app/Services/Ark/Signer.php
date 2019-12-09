<?php

namespace App\Services\Ark;

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
        return TransferBuilder::new()
            ->recipient($recipient)
            ->amount($amount)
            ->vendorField($purpose)
            ->withNonce($nonce)
            ->sign(decrypt(config('delegate.passphrase')))
            ->secondSign(decrypt(config('delegate.secondPassphrase')));
    }
}
