<?php

namespace App\Models\Networks;

use ArkEcosystem\Crypto\Networks\AbstractNetwork;

/*
 * Compendia Devnet
 */
class CompendiaDevnet extends AbstractNetwork
{
    /**
     * {@inheritdoc}
     *
     * @see Network::$base58PrefixMap
     */
    protected $base58PrefixMap = [
        self::BASE58_ADDRESS_P2PKH => '5a',
        self::BASE58_ADDRESS_P2SH  => '00',
        self::BASE58_WIF           => 'aa',
    ];

    /**
     * {@inheritdoc}
     *
     * @see Network::$bip32PrefixMap
     */
    protected $bip32PrefixMap = [
        self::BIP32_PREFIX_XPUB => '70617039',
        self::BIP32_PREFIX_XPRV => '70615956',
    ];

    /**
     * {@inheritdoc}
     */
    public function pubKeyHash(): int
    {
        return 90;
    }

    /**
     * {@inheritdoc}
     */
    public function epoch(): string
    {
        return '2020-08-28T11:30:00.000Z';
    }
}
