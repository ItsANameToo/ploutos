<?php

namespace Database\Factories;

use App\Models\Disbursement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DisbursementFactory extends Factory
{
    protected $model = Disbursement::class;

    public function definition(): array
    {
        return [
            'transaction_id' => Str::random(64),
            'amount'         => $this->faker->randomNumber(9),
            'signed_at'      => Carbon::now(),
            'purpose'        => Str::random(24),
            'transaction'    => [],
        ];
    }
}
