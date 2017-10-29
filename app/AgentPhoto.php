<?php

namespace App;

use App\Agent;
use App\ApiCall;
use Illuminate\Database\Eloquent\Model;

class AgentPhoto extends Model
{
    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo('App\Agent');
    }
}
