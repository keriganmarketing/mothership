<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impression extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo('App\Listing');
    }
}
