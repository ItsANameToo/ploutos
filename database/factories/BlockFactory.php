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

$factory->define(App\Models\Block::class, function (Faker $faker) {
    return [
        'block_id'     => $faker->randomNumber(9),
        'height'       => $faker->randomNumber(9),
        'reward'       => 2 * ARKTOSHI,
        'forged_at'    => Carbon::now(),
        // 'processed_at' => Carbon::now(),
    ];
});
