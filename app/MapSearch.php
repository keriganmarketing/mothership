<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class MapSearch extends Model
{
    /**
     * Return all listings for the map search
     *
     * @return \App\Listing $listing The listing object from storage
     */
    public static function getAllListings($request)
    {
        $city         = $request->city ?? '';
        $status       = $request->status ?? '';
        $propertyType = $request->propertyType ?? '';
        $minPrice     = $request->minPrice ?? '';
        $maxPrice     = $request->maxPrice ?? '';
        $beds         = $request->bedrooms ?? '';
        $baths        = $request->bathrooms ?? '';
        $sqft         = $request->sq_ft ?? '';
        $acreage      = $request->acreage ?? '';
        $waterfront   = $request->waterfront ?? '';
        $pool         = $request->pool ?? '';
        $sortBy       = $request->sortBy ?? 'date_modified';
        $orderBy      = $request->orderBy ?? 'DESC';

        if ($propertyType != '') {
            $propertyType = explode('|', $propertyType);
        }
        if ($status != '') {
            $status = explode('|', $status);
        }
        $listings = DB::table('listings')
            ->select('mls_account', 'latitude', 'longitude', 'status', 'class')
            ->when($city, function ($query) use ($city) {
                $query->where(function ($query) use ($city) {
                    $query->where('city', $city)
                        ->orWhere('zip', $city)
                        ->orWhere('sub_area', $city)
                        ->orWhere('area', $city)
                        ->orWhere('subdivision', $city)
                        ->orWhere('full_address', $city)
                        ->orWhere('mls_account', $city);
                });
            })
            ->when($propertyType, function ($query) use ($propertyType) {
                return $query->whereIn('property_type', $propertyType);
            })
            ->when($status, function ($query) use ($status) {
                return $query->whereIn('status', $status);
            })
            ->when($minPrice, function ($query) use ($minPrice) {
                return $query->where('price', '>=', $minPrice);
            })
            ->when($maxPrice, function ($query) use ($maxPrice) {
                return $query->where('price', '<=', $maxPrice);
            })
            ->when($beds, function ($query) use ($beds) {
                return $query->where('bedrooms', '>=', $beds);
            })
            ->when($baths, function ($query) use ($baths) {
                return $query->where('bathrooms', '>=', $baths);
            })
            ->when($sqft, function ($query) use ($sqft) {
                return $query->where('sq_ft', '>=', $sqft);
            })
            ->when($acreage, function ($query) use ($acreage) {
                return $query->where('acreage', '>=', $acreage);
            })
            ->when($waterfront, function ($query) use ($waterfront) {
                return $query->where('waterfront', true);
            })
            ->when($pool, function ($query) use ($pool) {
                return $query->where('pool', true);
            })
            ->get();

            return $listings;
    }
}
