<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Impression extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo('App\Listing');
    }

    public function logSingle($listing_id)
    {
        $today = Carbon::now()->toDateString();

        $click = Impression::where('listing_id', $listing_id)
            ->where('date', $today)->first();

        if ($click) {
            $click->increment('counter');
        } else {
            Impression::create([
                'listing_id' => $listing_id,
                'date'       => $today,
                'counter'    => 1
            ]);
        }
    }

    public function logMultiple($listings)
    {
        $today = Carbon::now()->toDateString();

        foreach ($listings as $listing) {
            $impression = Impression::where('listing_id', $listing->id)
                ->where('date', $today)->first();

            if ($impression) {
                $impression->increment('counter');
            } else {
                Impression::create([
                    'listing_id' => $listing->id,
                    'date'       => $today,
                    'counter'    => 1
                ]);
            }
        }
    }
}
