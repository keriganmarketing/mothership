<?php

namespace App\Http\Controllers;

use App\Listing;
use App\Agent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $numListings = Listing::select('id')->get()->count();
        $numAgents = Agent::select('id')->get()->count();
        $now = Carbon::now();

        return view('home', compact('now','numListings','numAgents'));
    }
}
