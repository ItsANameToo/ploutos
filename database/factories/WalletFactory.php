<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'address'            => Str::random(34),
            'public_key'         => Str::random(66),
            'balance'            => $this->faker->randomNumber(9),
            'earnings'           => $this->faker->randomNumber(9),
        ];
    }
}
