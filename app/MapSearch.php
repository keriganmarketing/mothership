<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class MapSearch extends Model
{
    public static function getAllListings()
    {
        $listings = DB::table('listings')
            ->select('mls_account', 'latitude', 'longitude', 'status', 'class')
            ->where('status', 'Active')
            ->get();

        return $listings;
    }
}
