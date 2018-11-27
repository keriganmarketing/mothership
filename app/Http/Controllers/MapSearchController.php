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

        $response = response()->json($listings)->withHeaders(
            [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
            
        return $response;
    }
}