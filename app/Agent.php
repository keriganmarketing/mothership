<?php

namespace App;

use App\ApiCall;
use App\Helpers\AgentsHelper;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $guarded = [];

    const SEARCH_OPTIONS = 'MEMBER_0,MEMBER_1,MEMBER_3,MEMBER_4,MEMBER_5,MEMBER_6,MEMBER_7,MEMBER_8,MEMBER_10,MEMBER_11,MEMBER_12,MEMBER_13,MEMBER_14,MEMBER_15,MEMBER_16,MEMBER_17,MEMBER_18,MEMBER_19,MEMBER_21,STATUS,ACTIVE,MLS_STATUS,LICENSE,TIMESTAMP,OFFICESHORT';

    /**
     * An agent has many photos
     *
     * @return \Illuminate\Database\Eloquent\hasMany
     */
    public function photos()
    {
        return $this->hasMany('App\AgentPhoto', 'agent_id');
    }

    /**
     * Return the requested results
     *
     * @param mixed $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchResults($request)
    {
        $shortId         = $request->shortId ?? '';
        $fullName        = $request->fullName ?? '';
        $lastName        = $request->lastName ?? '';
        $firstName       = $request->firstName ?? '';
        $association     = $request->association ?? '';
        $officeShortId   = $request->officeShortId ?? '';
        // Sometimes the full name isn't in the database, so we'll fake one using the request and then check the
        // first_name and last_name fields against it.
        $derivedFullName = $fullName != null ? explode(' ', $fullName) : ''; // Array(['First', 'Last'])


        $agents = Agent::when($shortId, function ($query) use ($shortId) {
            return $query->where('short_id', $shortId);
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
        ->when($fullName, function ($query) use ($fullName, $derivedFullName) {
            return $query->whereRaw(
                "((full_name LIKE '{$fullName}') OR (first_name LIKE '{$derivedFullName[0]}' AND last_name LIKE '{$derivedFullName[1]}'))"
            );
        })
        ->with('photos')
        ->orderBy('last_name', 'ASC')
        ->orderBy('date_modified', 'DESC')
        ->paginate(36);

        // returns paginated links (with GET variables intact!)
        $agents->appends($request->all())->links();

        return $agents;
    }

    /**
     * Return the agent for the given MLS ID
     *
     * @param string $mlsId
     * @return \App\Agent
     */
    public static function byMlsId($mlsId)
    {
        return Agent::where('agent_id', $mlsId)->first();
    }
}
