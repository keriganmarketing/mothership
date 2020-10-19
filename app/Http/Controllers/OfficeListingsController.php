<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

class OfficeListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $officeShortID = isset($request->officeId) && $request->officeId != '' ? $request->officeId : 'xxxx';
        $listings = Listing::forOffice($officeShortID, $request);

        return response()->json($listings);
    }
}
