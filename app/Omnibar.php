<?php

namespace App;

use App\Listing;
use Illuminate\Database\Eloquent\Model;

class Omnibar extends Model
{
    public static function build($searchTerm)
    {
        $data         = [];
        $cities       = Listing::getColumn($searchTerm, 'city');
        $areas        = Listing::getColumn($searchTerm, 'area');
        $subAreas     = Listing::getColumn($searchTerm, 'sub_area');
        $zips         = Listing::getColumn($searchTerm, 'zip');

        //format the array so that it returns the proper JSON response

        $data['results'] = [
            [
                'text'     => count($cities) > 0 ? 'City' : '',
                'children' => []
            ],
            [
                'text'     => count($areas) > 0 ? 'Area' : '',
                'children' => []
            ],
            [
                'text'     => count($subAreas) > 0 ? 'Sub Area' : '',
                'children' => []
            ],
            [
                'text'     => count($zips) > 0 ? 'Zip Code' : '',
                'children' => []
            ]
        ];

        foreach ($cities as $city) {
            $data['results'][0]['children'][] =
            [
                'id' => ucwords($city->city),
                'text' => ucwords($city->city)
            ];
        }
        foreach ($areas as $area) {
            $data['results'][1]['children'][] =
            [
                'id' => ucwords($area->area),
                'text' => ucwords($area->area)
            ];
        }
        foreach ($subAreas as $subArea) {
            $data['results'][2]['children'][] =
            [
                'id'   => ucwords($subArea->sub_area),
                'text' => ucwords($subArea->sub_area)
            ];
        }
        foreach ($zips as $zip) {
            $data['results'][3]['children'][] =
            [
                'id' => $zip->zip,
                'text' => $zip->zip
            ];
        }

        return $data;
    }
}
