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
}
