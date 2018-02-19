<?php

namespace App\Http\Controllers;

use App\Agent;
use Illuminate\Http\Request;

class AgentSearchController extends Controller
{
    /**
     * Return a listing of the searched agents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $agents = Agent::searchResults($request);

        return response()->json($agents->toArray());
    }

    protected function show(Request $request)
    {
        if ($request->email) {
            $agent = new Agent();

            return $agent->buildAgentData($request->email);
        }
    }
}
