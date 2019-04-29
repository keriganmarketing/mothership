<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Jobs\ProcessSearch;
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

        if(!preg_match_all( "/\b(bot)|(crawler)|(spider)|(curl)\b/i", $request->header('User-Agent'), $matches )){
            ProcessSearch::dispatch($request->all());
        }

        return response()->json($listings);
    }
}
