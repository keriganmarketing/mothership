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

    public static function savePhotos($agent, $photos)
    {
        foreach ($photos as $photo) {
            if (! $photo->isError()) {
                echo '*';
                AgentPhoto::create([
                    'agent_id'    => $agent->id,
                    'url'         => $photo->getLocation(),
                    'description' => $photo->getContentDescription() ?? 'No photo description provided'
                ]);
            }
        }
    }
}
