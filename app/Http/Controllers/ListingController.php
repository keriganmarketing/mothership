<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Listing;
use App\Jobs\ProcessListingClick;
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

        if(!preg_match_all( "/\b(bot|crawler|spider|rogerBot)\b/i", $request->header('User-Agent'), $matches )){
            ProcessListingClick::dispatch($listing);
        }

        return response()->json($listing);
    }
}
