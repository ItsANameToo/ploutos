<?php

namespace Tests\Unit;

use App\Models\Wallet;
use App\Services\Calculator;
use Tests\TestCase;

class CalculatorTest extends TestCase
{
    public function test_it_calculates_the_share_per_block(): void
    {
        config(['delegate.sharePercentage' => 90]);

        Wallet::factory()->create(['balance' => 300 * ARKTOSHI]);
        Wallet::factory()->create(['balance' => 700 * ARKTOSHI]);

        $calculator = app(Calculator::class)->setReward(2 * ARKTOSHI);

        $this->assertSame(54000000, $calculator->perBlock(300 * ARKTOSHI)->toInteger());
        $this->assertSame(126000000, $calculator->perBlock(700 * ARKTOSHI)->toInteger());
        $this->assertSame(180000000, $calculator->perBlock(1000 * ARKTOSHI)->toInteger());
    }

    public function test_it_rounds_half_down(): void
    {
        config(['delegate.sharePercentage' => 100]);

        Wallet::factory()->create(['balance' => 4]);

        $calculator = app(Calculator::class)->setReward(10);

        $this->assertSame(2, $calculator->perBlock(1)->toInteger());
        $this->assertSame(7, $calculator->perBlock(3)->toInteger());
    }
}
