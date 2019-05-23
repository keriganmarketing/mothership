<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Helpers\StatsHelper;
use Illuminate\Http\Request;

class ListingsSearchController extends Controller
{
    /**
     * Display the search results
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $listings = Listing::searchResults($request);
        //(new StatsHelper($request))->logSearch();

        return response()->json($listings);
    }
}
