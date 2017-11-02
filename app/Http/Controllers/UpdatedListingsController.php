<?php

namespace App\Http\Controllers;

use App\Listing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UpdatedListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = Carbon::now()->toDateString();
        $listings = Listing::whereDate('date_modified', $today)->get();

        return response()->json($listings);
    }
}
