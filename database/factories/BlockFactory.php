<?php

namespace Database\Factories;

use App\Models\Block;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    protected $model = Block::class;

    public function definition(): array
    {
        return [
            'block_id'     => $this->faker->randomNumber(9),
            'height'       => $this->faker->randomNumber(9),
            'reward'       => 2 * ARKTOSHI,
            'forged_at'    => Carbon::now(),
            // 'processed_at' => Carbon::now(),
        ];
    }
}
