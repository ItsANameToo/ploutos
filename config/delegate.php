<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Relay Host
    |--------------------------------------------------------------------------
    |
    | Here you may specify which host should be used to poll blocks, voters,
    | wallets and transactions and also broadcast transactions after signing.
    |
    */

    'host' => env('ARK_DELEGATE_HOST'),

    /*
    |--------------------------------------------------------------------------
    | Delegate Username
    |--------------------------------------------------------------------------
    |
    | Here you may specify the username of the forging delegate which will be
    | used to poll blocks, voters, transactions and use as a base for all data.
    |
    */

    'username' => env('ARK_DELEGATE_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | Delegate Address
    |--------------------------------------------------------------------------
    |
    | Here you may specify the address of the forging delegate which will be
    | used to poll blocks, voters, transactions and use as a base for all data.
    |
    */

    'address' => env('ARK_DELEGATE_ADDRESS'),

    /*
    |--------------------------------------------------------------------------
    | Delegate Public Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify the public key of the forging delegate which will be
    | used to poll blocks, voters, transactions and use as a base for all data.
    |
    */

    'publicKey' => env('ARK_DELEGATE_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Disbursement Vendor Field
    |--------------------------------------------------------------------------
    |
    | Here you may specify the vendor field that should be used when
    | disbursements get created and signed which will indicate the purpose
    | of the transactions the voters after receiving the transactions.
    |
    */

    'vendorField' => env('ARK_DELEGATE_VENDOR_FIELD'),

    /*
    |--------------------------------------------------------------------------
    | Reward Share Percentage
    |--------------------------------------------------------------------------
    |
    | Here you may specify which percentage of a block reward should be
    | shared with voters after receiving a new forged block.
    |
    */

    'sharePercentage' => env('ARK_DELEGATE_SHARE_PERCENTAGE', 90),

    /*
    |--------------------------------------------------------------------------
    | Disbursement Threshold
    |--------------------------------------------------------------------------
    |
    | Here you may specify which threshold has to be met in order for a voter
    | to be eligible for a payout. This value should be adjusted as the price
    | of ARK goes up to avoid wasting ARK on fees.
    |
    */

    'threshold' => env('ARK_DELEGATE_THRESHOLD', 0.1),

    /*
    |--------------------------------------------------------------------------
    | Wallet Passphrase
    |--------------------------------------------------------------------------
    |
    | Here you may specify the passphrase of the wallet that will be used to
    | sign transactions. Make sure that you encrypt passphrase!
    |
    */

    'passphrase' => env('ARK_DELEGATE_PASSPHRASE'),

    /*
    |--------------------------------------------------------------------------
    | Wallet Second Passhrase
    |--------------------------------------------------------------------------
    |
    | Here you may specify the second passphrase of the wallet that will be
    | used to sign transactions. Make sure that you encrypt passphrase!
    |
    */

    'secondPassphrase' => env('ARK_DELEGATE_SECOND_PASSPHRASE'),

    /*
    |--------------------------------------------------------------------------
    | Staking
    |--------------------------------------------------------------------------
    |
    | Here you may specify if you want calculations to be done with a voters
    | full stake which is (wallet balance + outstanding earnings) or if you
    | wish to only take the wallet balance into account.
    |
    */

    'staking' => env('ARK_DELEGATE_STAKING', false),

    /*
    |--------------------------------------------------------------------------
    | Fees
    |--------------------------------------------------------------------------
    |
    | Here you may specify if you cover fees or not, which will result in
    | different payouts for voters and delegate itself.
    |
    */

    'fees' => [

        /*
        |-----------------------------------------------------------------------
        | Fee Coverage
        |-----------------------------------------------------------------------
        |
        | Here you may specify if you cover fees or not, which will result in
        | different payouts for voters and delegate itself.
        |
        */

        'cover' => env('ARK_DELEGATE_FEE_COVER', false),

        /*
        |-----------------------------------------------------------------------
        | Delegate Payout Fee Deduction
        |-----------------------------------------------------------------------
        |
        | Here you may specify if you want the fees deducted from your delegate
        | share payout. Normally you want this set to true when you cover the
        | fees of your voters.
        |
        */

        'deduct' => env('ARK_DELEGATE_FEE_DEDUCT', false),

        /*
        |-----------------------------------------------------------------------
        | Fee Sharing
        |-----------------------------------------------------------------------
        |
        | Here you may specify if you want forged fees to be shared, which will
        | result in different payouts for voters and delegate itself.
        |
        */

        'share' => env('ARK_DELEGATE_FEE_SHARE', false),

    ],

    /*
    |--------------------------------------------------------------------------
    | Wallet Whitelist
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the wallets below you wish to include
    | in public listings, calculations and all wallet operations. This only
    | impacts wallets that would normally be banned.
    |
    */

    'whitelist' => [],

    /*
    |--------------------------------------------------------------------------
    | Wallet Blacklist
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the wallets below you wish to exclude
    | from public listings, calculations and all wallet operations. Usually
    | this should only be your delegate and private wallets.
    |
    */

    'blacklist' => [],

    /*
    |--------------------------------------------------------------------------
    | Personal Delegate Wallet
    |--------------------------------------------------------------------------
    |
    | Here you may specify the personal wallet & profit share you are taking
    | of daily production for development & maintenance of the delegate.
    |
    */

    'personal' => [
        'address'         => env('ARK_DELEGATE_PERSONAL_ADDRESS'),
        'vendorField'     => env('ARK_DELEGATE_PERSONAL_VENDOR_FIELD'),
        'sharePercentage' => env('ARK_DELEGATE_PERSONAL_SHARE_PERCENTAGE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Polling
    |--------------------------------------------------------------------------
    |
    | Here you may specify what data you want to be polled on a regular bases.
    | This is only useful if you want historical data to generate reports of
    | averages, sums and other kind of aggregate data.
    |
    */

    'polling' => [
        'transactions' => env('ARK_DELEGATE_POLL_TRANSACTIONS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Distribution
    |--------------------------------------------------------------------------
    |
    | Here you may specify which shares you would like to have distributed
    | on a regular basis. Currently this only makes sense for the blacklisted
    | & banned wallets as you can spread their share to your loyal voters
    | after they have unvoted you and are still left with some earnings.
    |
    */

    'distribute' => [
        'blacklist' => env('ARK_DELEGATE_DISTRIBUTE_BLACKLIST', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcast type
    |--------------------------------------------------------------------------
    |
    | Here you may specify in what way the transactions are broadcasted.
    | Current options are 'default', where all transactions are sent to the
    | host node specified in your .env file, and 'spread', in which case the
    | transactions are sent to multiple peers.
    |
    */

    'broadcastType' => env('ARK_DELEGATE_BROADCAST_TYPE', 'default'),

];
