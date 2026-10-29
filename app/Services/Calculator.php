<?php

namespace App\Services;

use App\Models\Wallet;

class Calculator
{
    /**
     * @var int
     */
    protected $profitShare;

    /**
     * @var int
     */
    protected $votingPool;

    /**
     * @var int
     */
    protected $reward = 2 * ARKTOSHI;

    /**
     * Create a new calculator instance.
     */
    public function __construct()
    {
        $this->votingPool = (int) Wallet::public()->sum('balance');
        $this->profitShare = config('delegate.sharePercentage');
    }

    /**
     * Calculate the profit share for a single block.
     *
     * @param int $value
     *
     * @return \App\Services\BigNumber
     */
    public function perBlock(int $value): BigNumber
    {
        if ($value >= $this->votingPool) {
            return BigNumber::create($this->reward)
                ->times($this->profitShare / 100);
        }

        return BigNumber::create($this->reward)
            ->times($this->profitShare / 100)
            ->times($value)
            ->dividedBy($this->votingPool);
    }

    /**
     * Set the profit share percentage.
     *
     * @param int $value
     *
     * @return \App\Services\Calculator
     */
    public function setProfitShare(int $value): self
    {
        $this->profitShare = $value;

        return $this;
    }

    /**
     * Set the reward per block.
     *
     * @param int $value
     *
     * @return \App\Services\Calculator
     */
    public function setReward(int $value): self
    {
        $this->reward = $value;

        return $this;
    }
}
