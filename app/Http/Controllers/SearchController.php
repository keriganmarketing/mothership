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
        $city       = $request->city;
        $status     = $request->status;
        $class      = explode('|', $request->class);
        $minPrice   = $request->minPrice;
        $maxPrice   = $request->maxPrice;

        if (! is_array($class)) {
            $class = $class->toArray();
        }

        $listings = Listing:: when($city, function ($query) use ($city) {
                $query->where(function ($query) use ($city) {
                    $query->where('city', $city)
                            ->orWhere('zip', $city)
                            ->orWhere('sub_area', $city)
                            ->orWhere('area', $city)
                            ->orWhere('subdivision', $city);
                });
        })
            ->when($class, function ($query) use ($class) {
                return $query->whereIn('property_type', $class);
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
            ->paginate(36);

        // returns paginated links (with GET variables intact!)
        $listings->appends($request->all())->links();

        return response()->json($listings->toArray());
    }
}
