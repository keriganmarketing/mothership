<?php

namespace App;

use carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo('App\Listing');
    }

    public function logNew($listing_id)
    {
        $today = Carbon::now()->toDateString();

        $view = View::where('listing_id', $listing_id)
            ->where('date', $today)->first();

        if ($view) {
            $view->increment('counter');
        } else {
            View::create([
                'listing_id' => $listing_id,
                'date'       => $today,
                'counter'    => 1
            ]);
        }
    }
}
