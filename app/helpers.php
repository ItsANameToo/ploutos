<?php

use App\Models\Block;
use App\Models\Networks\CompendiaDevnet;
use App\Models\Networks\CompendiaMainnet;
use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\Devnet;
use Illuminate\Support\Carbon;

/**
 * Divide the specified value by arktoshi and format it.
 *
 * @param int $value
 * @param int $decimals
 *
 * @return string
 */
function format_arktoshi(int $value, int $decimals = 8)
{
    return number_format($value / ARKTOSHI, $decimals);
}

function set_crypto_network(): void
{
    $network = config('delegate.network');

    if ($network === 'mainnet') {
        return;
    }

    if ($network === 'devnet') {
        Network::set(new Devnet());
    }

    if ($network === 'compendia_mainnet') {
        Network::set(new CompendiaMainnet());
    }

    if ($network === 'compendia_devnet') {
        Network::set(new CompendiaDevnet());
    }
}

/**
 * Make an Ark epoch human readable.
 *
 * @param int $value
 *
 * @return string
 */
function humanize_epoch(int $value)
{
    return Carbon::parse(Network::get()->epoch())->addSeconds($value);
}

/**
 * Convert an object to its array representation.
 *
 * @param object $value
 *
 * @return array
 */
function object_to_array(object $value): array
{
    return json_decode(json_encode($value), true);
}

/**
 * Transform a transfer into its generic representation.
 *
 * @param array $value
 *
 * @return array
 */
function transform_transfer(array $value): array
{
    return array_only($value, [
        'type', 'typeGroup', 'expiration', 'nonce', 'amount', 'fee',
        'recipientId', 'vendorField', 'senderPublicKey', 'signature',
        'signSignature', 'id', 'version', 'network',
    ]);
}

/**
 * Transform a transfer into its generic representation.
 *
 * @param \App\Models\Block $block
 */
function calculate_delegate_share(Block $block): void
{
    $delegatePercentage = env('ARK_DELEGATE_PERSONAL_SHARE_PERCENTAGE');

    if ($delegatePercentage > 0) {
        $delegatePercentage /= 100;

        cache()->increment('delegate.earnings', $block->total * $delegatePercentage);
    }
}
