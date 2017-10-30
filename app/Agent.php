<?php

namespace App;

use App\ApiCall;
use App\Http\Helpers\AgentsHelper;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $guarded = [];

    public function photos()
    {
        return $this->hasMany('App\AgentPhoto', 'agent_id');
    }

    public static function searchResults($request)
    {
        $shortId         = $request->shortId ?? '';
        $fullName        = $request->fullName ?? '';
        $lastName        = $request->lastName ?? '';
        $firstName       = $request->firstName ?? '';
        $association     = $request->association ?? '';
        $officeShortId   = $request->officeShortId ?? '';
        $derivedFullName = $fullName != null ? explode(' ', $fullName) : '';


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
        ->paginate(36);

        // returns paginated links (with GET variables intact!)
        $agents->appends($request->all())->links();

        return $agents;
    }
}
