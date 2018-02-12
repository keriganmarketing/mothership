<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;

class AgentListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agentShortId = $request->agentId;

        $listings = Listing::forAgent($agentShortId);

        return response()->json($listings)->withHeaders(
            [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
}
