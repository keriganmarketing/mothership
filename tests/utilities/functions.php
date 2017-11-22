<?php
function create($class, $attributes = [])
{
    return factory($class)->create($attributes);
}

function make($class, $attributes = [])
{
    return factory($class)->make($attributes);
}

function getPropertyTypes ($class = null) {
    $typeArray = [
        'Single Family Home'   => ['Detached Single Family'],
        'Condo / Townhome'     => ['Condominium', 'Townhouse', 'Townhomes'],
        'Commercial'           => ['Office', 'Retail', 'Industrial', 'Income Producing', 'Unimproved Commercial', 'Business Only', 'Auto Repair', 'Improved Commercial', 'Hotel/Motel'],
        'Lots / Land'          => ['Vacant Land', 'Residential Lots', 'Land', 'Land/Acres', 'Lots/Land'],
        'Multi-Family Home'    => ['Duplex Multi-Units', 'Triplex Multi-Units'],
        'Rental'               => ['Apartment', 'House', 'Duplex', 'Triplex', 'Quadruplex', 'Apartments/Multi-family'],
        'Manufactured'         => ['Mobile Home', 'Mobile/Manufactured'],
        'Farms / Agricultural' => ['Farm', 'Agricultural', 'Farm/Ranch', 'Farm/Timberland'],
        'Other'                => ['Attached Single Unit', 'Attached Single Family', 'Dock/Wet Slip', 'Dry Storage', 'Mobile/Trailer Park', 'Mobile Home Park', 'Residential Income', 'Parking Space', 'RV/Mobile Park']
    ];

    if ($class != null) {
        return $typeArray[$class];
    }

    return $typeArray;
}
