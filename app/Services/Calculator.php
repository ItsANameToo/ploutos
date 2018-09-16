<?php

namespace App\Services;

use App\Models\Wallet;
use ArkX\Calculus\Calculator as BaseCalculator;

class Calculator extends BaseCalculator
{
    /**
     * Create a new calculator instance.
     *
     * @param int $votingPool
     * @param int $profitShare
     */
    public function __construct()
    {
        $this->votingPool = Wallet::public()->sum('balance');
        $this->profitShare = config('delegate.sharePercentage');
    }
}
