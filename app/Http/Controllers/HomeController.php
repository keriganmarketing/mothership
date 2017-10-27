<?php

namespace App\Http\Controllers;

use App\Listing;
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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotListings = Listing::hotListings();

        return view('home', compact('hotListings'));
    }
}
