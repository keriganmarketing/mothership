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
        $subdivisions = Listing::getColumn($searchTerm, 'subdivision');
        $zips         = Listing::getColumn($searchTerm, 'zip');

        //format the array so that it returns the proper JSON response

        $data['results'] = [
            [
                'text'     => 'City',
                'children' => []
            ],
            [
                'text'     => 'Area',
                'children' => []
            ],
            [
                'text'     => 'Sub Area',
                'children' => []
            ],
            [
                'text'     => 'Subdivisions',
                'children' => []
            ],
            [
                'text'     => 'Zip Code',
                'children' => []
            ]
        ];

        if (count($cities) > 0) {
            foreach ($cities as $city) {
                $data['results'][0]['children'][] =
                [
                    'id' => ucwords($city->city),
                    'text' => ucwords($city->city)
                ];
            }
        }
        if (count($areas) > 0) {
            foreach ($areas as $area) {
                $data['results'][1]['children'][] =
                [
                    'id' => ucwords($area->area),
                    'text' => ucwords($area->area)
                ];
            }
        }
        if (count($subAreas) > 0) {
            foreach ($subAreas as $subArea) {
                $data['results'][2]['children'][] =
                [
                    'id'   => ucwords($subArea->sub_area),
                    'text' => ucwords($subArea->sub_area)
                ];
            }
        }
        if (count($subdivisions) > 0) {
            foreach ($subdivisions as $subdivision) {
                $data['results'][3]['children'][] =
                [
                    'id'   => ucwords($subdivision->subdivision),
                    'text' => ucwords($subdivision->subdivision)
                ]
            }
        }
        if (count($zips) > 0) {
            foreach ($zips as $zip) {
                $data['results'][3]['children'][] =
                [
                    'id' => $zip->zip,
                    'text' => $zip->zip
                ];
            }
        }

        return $data;
    }
}
