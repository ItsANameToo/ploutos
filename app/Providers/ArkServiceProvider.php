<?php

namespace App\Providers;

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Networks\Mainnet;
use Illuminate\Support\ServiceProvider;

class ArkServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        Network::set(Mainnet::new());
    }

    /**
     * Register services.
     */
    public function register()
    {
        if (!defined('ARKTOSHI')) {
            define('ARKTOSHI', 10 ** 8);
        }
    }
}
