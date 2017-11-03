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
        // $subAreas     = Listing::getColumn($searchTerm, 'sub_area');
        $subdivisions = Listing::getColumn($searchTerm, 'subdivision');
        $zips         = Listing::getColumn($searchTerm, 'zip');
        $addresses    = Listing::getColumn($searchTerm, 'full_address');
        $mlsNumbers   = Listing::getColumn($searchTerm, 'mls_account');
        $counter      = 0;

        //format the array so that it returns the proper JSON response

        $data['results'] = [];

        if (count($cities) > 0) {
            $data['results'][$counter] =
            [
                'text'     => 'City',
                'children' => []
            ];
            foreach ($cities as $city) {
                $data['results'][$counter]['children'][] =
                [
                    'id' => ucwords($city->city),
                    'text' => ucwords($city->city)
                ];
            }
            $counter = $counter +1;
        }
        if (count($areas) > 0) {
            $data['results'][$counter] =
            [
                'text'     => 'Area',
                'children' => []
            ];
            foreach ($areas as $area) {
                $data['results'][$counter]['children'][] =
                [
                    'id' => ucwords($area->area),
                    'text' => ucwords($area->area)
                ];
            }
            $counter = $counter +1;
        }
        if (count($subAreas) > 0) {
            $data['results'][$counter] =
            [
                'text'     => 'Sub Area',
                'children' => []
            ];
            foreach ($subAreas as $subArea) {
                $data['results'][$counter]['children'][] =
                [
                    'id' => ucwords($subArea->sub_area),
                    'text' => ucwords($subArea->sub_area)
                ];
            }
            $counter = $counter +1;
        }
        if (count($subdivisions) > 0) {
            $data['results'][$counter] =
            [
                'text'     => 'Subdivision',
                'children' => []
            ];
            foreach ($subdivisions as $subdivision) {
                $data['results'][$counter]['children'][] =
                [
                    'id' => ucwords($subdivision->subdivision),
                    'text' => ucwords($subdivision->subdivision)
                ];
            }
            $counter = $counter +1;
        }
        if (count($zips) > 0) {
            $data['results'][$counter] =
            [
                'text'     => 'Zip',
                'children' => []
            ];
            foreach ($zips as $zip) {
                $data['results'][$counter]['children'][] =
                [
                    'id' => ucwords($zip->zip),
                    'text' => ucwords($zip->zip)
                ];
            }
            $counter = $counter +1;
        }
        if (count($addresses) > 0) {
            $data['results'][$counter] =
            [
                'text'     => 'Address',
                'children' => []
            ];
            foreach ($addresses as $address) {
                $data['results'][$counter]['children'][] =
                [
                    'id' => ucwords($address->full_address),
                    'text' => ucwords($address->full_address)
                ];
            }
            $counter = $counter +1;
        }
        if (count($mlsNumbers) > 0) {
            $data['results'][$counter] =
            [
                'text'     => 'MLS ID',
                'children' => []
            ];
            foreach ($mlsNumbers as $mlsNumber) {
                $data['results'][$counter]['children'][] =
                [
                    'id' => ucwords($mlsNumber->mls_account),
                    'text' => ucwords($mlsNumber->mls_account)
                ];
            }
            $counter = $counter +1;
        }
        if (count($data['results']) == 0) {
            $data['results'][0] =
                [
                    'text' => 'No results for your search',
                    'children' => []
                ];
        }

        return $data;
    }
}
