<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use App\Jobs\ProcessImpression;

class SearchController extends Controller
{
    /**
     * Display the search results
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $listings = Listing::searchResults($request);

        return response()->json($listings);
    }
}
