<?php

use Faker\Generator as Faker;

$factory->define(App\Agent::class, function (Faker $faker) {

    $firstName    = $faker->firstName;
    $lastName     = $faker->lastName;
    $fullName     = $firstName . ' ' . $lastName;

    return [
        'agent_id'        => $faker->randomNumber(6, true),
        'office_id'       => $faker->randomNumber(6, true),
        'first_name'      => $firstName,
        'last_name'       => $lastName,
        'office_phone'    => $faker->phoneNumber,
        'cell_phone'      => $faker->phoneNumber,
        'home_phone'      => $faker->phoneNumber,
        'fax'             => $faker->phoneNumber,
        'email'           => $faker->companyEmail,
        'url'             => $faker->url,
        'street_1'        => $faker->streetAddress,
        'street_2'        => $faker->streetName,
        'city'            => $faker->city,
        'state'           => $faker->state,
        'zip'             => $faker->postcode,
        'short_id'        => $faker->randomNumber(5, true),
        'middle_name'     => $faker->firstName,
        'full_name'       => $fullName,
        'primary_phone'   => $faker->phoneNumber,
        'active_status'   => $faker->boolean(),
        'active'          => $faker->boolean(),
        'mls_status'      => $faker->boolean(),
        'license_number'  => $faker->randomNumber(8, true),
        'date_modified'   => $faker->dateTimeThisYear(),
        'office_short_id' => $faker->randomNumber(4, true),
        'association'     => $faker->randomElement(['bcar', 'ecar'])
    ];
});
