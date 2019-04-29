<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Jobs\ProcessSearch;
use App\Helpers\StatsHelper;
use Illuminate\Http\Request;
use App\Jobs\ProcessImpression;

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

        if((new StatsHelper())->isNotBot($request)){
            ProcessSearch::dispatch($request->all());
        }

        return response()->json($listings);
    }
}
