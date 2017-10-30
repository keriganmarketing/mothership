<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Listing;
use App\Jobs\ProcessListingClick;
use Illuminate\Http\Request;

class ListingController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \App\Listing  $listing
     * @return \Illuminate\Http\Response
     */
    public function show($mlsNumber)
    {
        $listing = Listing::where('mls_account', $mlsNumber)->with('photos')->first();

        ProcessListingClick::dispatch($listing);

        return response()->json($listing);
    }
}
