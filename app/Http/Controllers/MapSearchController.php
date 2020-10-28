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
        $listings = MapSearch::getAllListings($request);

        return response()->json($listings)->withHeaders(
            [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }

    public function show(Request $request)
    {
        $listings = MapSearch::geoJson($request);
        return response()->json($listings)->withHeaders(
            [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
}