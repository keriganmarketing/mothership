<?php

namespace App\Http\Controllers;

use App\Agent;
use Illuminate\Http\Request;

class AgentSearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shortId       = $request->shortId ?? '';
        $fullName      = $request->fullName ?? '';
        $lastName      = $request->lastName ?? '';
        $firstName     = $request->firstName ?? '';
        $association   = $request->association ?? '';
        $officeShortId = $request->officeShortId ?? '';

        $agents = Agent::when($shortId, function ($query) use ($shortId) {
            return $query->where('short_id', $shortId);
        })
        ->when($fullName, function ($query) use ($fullName) {
            return $query->where('full_name', $fullName);
        })
        ->when($lastName, function ($query) use ($lastName) {
            return $query->where('last_name', $lastName);
        })
        ->when($firstName, function ($query) use ($firstName) {
            return $query->where('first_name', $firstName);
        })
        ->when($association, function ($query) use ($association) {
            return $query->where('association', $association);
        })
        ->when($officeShortId, function ($query) use ($officeShortId) {
            return $query->where('office_short_id', $officeShortId);
        })
        ->with('photos')
        ->orderBy('last_name', 'ASC')
        ->paginate(36);

        // returns paginated links (with GET variables intact!)
        $agents->appends($request->all())->links();

        return response()->json($agents->toArray());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
}
