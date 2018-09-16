<?php

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

$factory->define(App\Models\Wallet::class, function (Faker $faker) {
    return [
        'address'            => str_random(34),
        'public_key'         => str_random(66),
        'balance'            => $faker->randomNumber(9),
        'earnings'           => $faker->randomNumber(9),
    ];
});
