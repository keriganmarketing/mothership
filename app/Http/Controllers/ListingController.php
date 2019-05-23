<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Listing;
use App\Helpers\StatsHelper;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $listings = Listing::findByMlsNumbers($request->mlsNumbers);

        return response()->json($listings);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $mlsNumber)
    {
        $listing = Listing::where('mls_account', $mlsNumber)->with(['photos', 'openHouses', 'agent'])->first();
        //(new StatsHelper($request))->logView($listing);

        return response()->json($listing);
    }

    public function click(Request $request, $mlsNumber)
    {
        $listing = Listing::where('mls_account', $mlsNumber)->first();
        //(new StatsHelper($request))->logClick($listing);
    }

    public function impression(Request $request, $mlsNumber)
    {
        $listing = Listing::where('mls_account', $mlsNumber)->first();
        //(new StatsHelper($request))->logImpression($listing);
    }
}
