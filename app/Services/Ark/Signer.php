<?php

namespace App\Services\Ark;

use ArkEcosystem\Crypto\Transactions\Builder\Transfer;
use ArkEcosystem\Crypto\Transactions\Builder\Vote;

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
    public function sign(string $recipient, int $amount, string $purpose): Transfer
    {
        return Transfer::new()
            ->recipient($recipient)
            ->amount($amount)
            ->vendorField($purpose)
            ->sign(decrypt(config('delegate.passphrase')))
            ->secondSign(decrypt(config('delegate.secondPassphrase')));
    }

    public function signVote(string $delegate): Vote
    {
        return Vote::new()
            ->votes(['+' . $delegate])
            ->sign(decrypt(config('delegate.passphrase')))
            ->secondSign(decrypt(config('delegate.secondPassphrase')));
    }

    public function signUnvote(string $delegate): Vote
    {
        return Vote::new()
            ->votes(['-' . $delegate])
            ->sign(decrypt(config('delegate.passphrase')))
            ->secondSign(decrypt(config('delegate.secondPassphrase')));
    }
}
