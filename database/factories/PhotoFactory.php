<?php

use Faker\Generator as Faker;

$factory->define(App\Photo::class, function (Faker $faker) {

    return [
        'url'               => $faker->url,
        'preferred'         => $faker->boolean(),
        'photo_description' => $faker->sentence
    ];
});
