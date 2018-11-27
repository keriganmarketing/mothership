<?php

namespace App\Http\Controllers;

use App\MapSearch;
use Illuminate\Http\Request;

class MapSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$listings = MapSearch::getAllListings($request);
        $listings = Listings::where('status=Active')->get();

        $response = response()->json($listings, 200,
            [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
            
        return $response;
    }
}
