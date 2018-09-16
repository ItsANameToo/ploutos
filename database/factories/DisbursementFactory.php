<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Disbursement::class, function (Faker $faker) {
    return [
        'transaction_id' => str_random(64),
        'amount'         => $faker->randomNumber(9),
        'signed_at'      => Carbon::now(),
        'purpose'        => str_random(24),
        'transaction'    => [],
    ];
});
