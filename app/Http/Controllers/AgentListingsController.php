<?php

namespace App\Http\Controllers;

use App\Listing;
use Illuminate\Http\Request;
use App\Impression;
use App\Click;
use App\View;

class AgentListingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agentShortId = isset($request->agentId) && $request->agentId != '' ? $request->agentId : 'xxxx';

        $listings = Listing::forAgent($agentShortId, $request);
        if ($request->analytics) {
            foreach ($listings as $listing) {
                $listing->impressions = Impression::where('listing_id', $listing->id)->pluck('counter')->sum();
                $listing->clicks = Click::where('listing_id', $listing->id)->pluck('counter')->sum();
                $listing->views = View::where('listing_id', $listing->id)->pluck('counter')->sum();
            }
        }

        return response()->json($listings)->withHeaders(
            [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }

    /**
     * Display sold listings of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sold(Request $request)
    {
        $agentShortId = isset($request->agentId) && $request->agentId != '' ? $request->agentId : 'xxxx';

        $listings = Listing::soldByAgent($agentShortId);

        return response()->json($listings)->withHeaders(
            [
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }

}
