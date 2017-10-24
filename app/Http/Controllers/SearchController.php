<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display the search results
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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

        if ($propertyType != '') {
            $propertyType = explode('|', $propertyType);
        }

        $listings = Listing::when($city, function ($query) use ($city) {
            $query->where(function ($query) use ($city) {
                $query->where('city', $city)
                    ->orWhere('zip', $city)
                    ->orWhere('sub_area', $city)
                    ->orWhere('area', $city)
                    ->orWhere('subdivision', $city);
            });
        })
        ->when($propertyType, function ($query) use ($propertyType) {
            return $query->whereIn('property_type', $propertyType);
        })
        ->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
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
        ->paginate(36);

        // returns paginated links (with GET variables intact!)
        $listings->appends($request->all())->links();

        return response()->json($listings->toArray());
    }
}
