<?php

use Faker\Generator as Faker;
use App\Listing;

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

$factory->define(App\Listing::class, function (Faker $faker) {

    return  [
        'mls_account'              => (string) $faker->randomNumber(6, true),
        'price'                    => $faker->numberBetween(100000, 9000000),
        'area'                     => $faker->city,
        'sub_area'                 => $faker->city,
        'subdivision'              => $faker->city,
        'city'                     => $faker->city,
        'street_number'            => $faker->randomNumber(3),
        'street_name'              => $faker->streetName,
        'unit_number'              => $faker->randomNumber(2),
        'state'                    => $faker->state,
        'zip'                      => $faker->postcode,
        'latitude'                 => $faker->latitude,
        'longitude'                => $faker->longitude,
        'bedrooms'                 => $faker->randomDigit,
        'bathrooms'                => $faker->randomDigit,
        'sq_ft'                    => $faker->numberBetween(100, 2000),
        'acreage'                  => $faker->randomNumber(2, false),
        'class'                    => $faker->randomElement (['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I', 'J']),
        'property_type'            => 'Detached Single Family',
        'status'                   => 'Active',
        'waterfront'               => $faker->boolean(),
        'foreclosure'              => $faker->boolean(),
        'garage'                   => $faker->boolean(),
        'pool'                     => $faker->boolean(),
        'distressed'               => $faker->boolean(),
        'date_modified'            => $faker->dateTimeThisMonth(),
        'agent_id'                 => $faker->randomNumber(9, true),
        'colist_agent_id'          => $faker->randomNumber(9, true),
        'office_id'                => $faker->randomDigit(9, true),
        'colist_office_id'         => $faker->randomDigit(9, true),
        'list_date'                => $faker->date(),
        'sold_date'                => $faker->date(),
        'sold_price'               => $faker->numberBetween(100000, 9000000),
        'association'              => 'bcar',
        'listing_member_shortid'   => 'B1065',
        'colisting_member_shortid' => 'B1032',
        'interior'                 => $faker->sentence,
        'appliances'               => $faker->sentence,
        'amenities'                => $faker->sentence,
        'exterior'                 => $faker->sentence,
        'lot_description'          => $faker->sentence,
        'energy_features'          => $faker->sentence,
        'construction'             => $faker->sentence,
        'utilities'                => $faker->sentence,
        'zoning'                   => $faker->sentence,
        'waterview_description'    => $faker->sentence,
        'elementary_school'        => $faker->company,
        'middle_school'            => $faker->company,
        'high_school'              => $faker->company,
        'sqft_source'              => 'Source',
        'year_built'               => $faker->year(),
        'lot_dimensions'           => '140 x 304',
        'stories'                  => $faker->randomDigit,
        'full_baths'               => $faker->randomDigit,
        'half_baths'               => $faker->randomDigit,
        'last_taxes'               => $faker->year(),
        'last_tax_year'            => $faker->year(),
        'description'              => $faker->sentence,
        'apn'                      => '324242342342342',
        'directions'               => $faker->paragraph,
        'full_address'             => $faker->streetAddress
    ];
});
