<?php

namespace App;

use App\Listing;
use Illuminate\Database\Eloquent\Model;

class Omnibar extends Model
{
    public static function build()
    {
        $data     = [];
        $cities   = Listing::getColumn('city');
        $areas    = Listing::getColumn('area');
        $subAreas = Listing::getColumn('sub_area');
        $zips     = Listing::getColumn('zip');

        //format the array so that it returns the proper JSON response

        $data['results'] = [
            [
                'text' => 'City',
                'children' => []
            ],
            [
                'text' => 'Area',
                'children' => []
            ],
            [
                'text' => 'Sub Area',
                'children' => []
            ],
            [
                'text' => 'Zip Code',
                'children' => []
            ]
        ];

        foreach ($cities as $city) {
            $data['results'][0]['children'][] = [
                    'id' => $city->city,
                    'text' => $city->city
                ];
        }
        foreach ($areas as $area) {
            $data['results'][1]['children'][] = [
                    'id' => $area->area,
                    'text' => $area->area
                ];
        }
        foreach ($subAreas as $subArea) {
            $data['results'][2]['children'][] = [
                    'id' => $subArea->sub_area,
                    'text' => $subArea->sub_area
                ];
        }
        foreach ($zips as $zip) {
            $data['results'][3]['children'][] = [
                    'id' => $zip->zip,
                    'text' => $zip->zip
                ];
        }

        return $data;
    }
}
