<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo('App\Listing');
    }

    public function logNew($listing_id)
    {
        $today = Carbon::now()->toDateString();

        $click = Click::where('listing_id', $listing_id)
            ->where('date', $today)->first();

        if ($click) {
            $click->increment('counter');
        } else {
            Click::create([
                'listing_id' => $listing_id,
                'date'       => $today,
                'counter'    => 1
            ]);
        }
    }
}
