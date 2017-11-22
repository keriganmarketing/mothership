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

$factory->define(App\Photo::class, function (Faker $faker) {

    return [
        'url'               => $faker->url,
        'preferred'         => $faker->boolean(),
        'photo_description' => $faker->sentence
    ];
});
