<?php

use Faker\Generator as Faker;

$factory->define(App\Photo::class, function (Faker $faker) {

    $listing = factory('App\Listing')->create();

    return [
        'listing_id'        => $listing->id,
        'mls_account'       => $listing->mls_account,
        'url'               => $faker->url,
        'preferred'         => $faker->boolean(),
        'photo_description' => $faker->sentence
    ];
});
