<?php

namespace App;

use App\Click;
use App\Photo;
use App\ApiCall;
use App\Listing;
use Carbon\Carbon;
use App\Http\Helpers\BcarOptions;
use App\Http\Helpers\EcarOptions;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\ListingsHelper;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $guarded = [];

    /**
     * A Listing has many photos
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    public function clicks()
    {
        return $this->hasMany('App\Click', 'listing_id');
    }

    public function impresssions()
    {
        return $this->hasMany('App\Impression', 'listing_id');
    }

    /**
     * Return a specific column from the listings database
     *
     * @param string $columnName The name of the column
     *
     * @return void
     */
    public static function getColumn($searchTerm, $columnName)
    {
        $values  = DB::table('listings')
        ->selectRaw("DISTINCT LOWER({$columnName}) as {$columnName}")
        ->whereRaw("LOWER({$columnName}) LIKE LOWER('%{$searchTerm}%')")
        ->get();

        return $values->toArray();
    }

    public static function forAgent($agentShortId)
    {
        if (preg_match('/|/', $agentShortId)) {
            $ids = explode('|', $agentShortId);
        } else {
            $ids = [$agentShortId];
        }
        $listings = Listing::whereIn('listing_member_shortid', $ids)
            ->orWhereIn('colisting_member_shortid', $ids)
            ->get();

        return $listings;
    }

    public function buildFullAddress(Listing $listing)
    {
        $streetNumber = $listing->street_number;
        $streetName   = ucwords(strtolower($listing->street_name));
        $city         = ', '. $listing->city;
        $fullAddress  = $streetNumber . ' ' . $streetName . $city;

        if ($this->addressIsValid($fullAddress)) {
            return $fullAddress;
        }

        return null;
    }

    public function addressIsValid($fullAddress)
    {
        return preg_match('/^[1-9]+([0-9]*)?\s(\d*?)([A-Z]+)?[a-z].+$/', $fullAddress);
    }

    public static function hotListings()
    {
        $hotListings = [];

        $now = Carbon::now();

        $clickedListings = Click::
            whereDate('created_at', '>=', $now->copy()->subDays(7))
            ->whereDate('created_at', '<=', $now)
            ->groupBy('listing_id')
            ->pluck('listing_id');

        foreach ($clickedListings as $listingId) {
            $clicks = Click::where('listing_id', $listingId)->count();
            $hotListings[$listingId] = $clicks;
        }

        arsort($hotListings);

        echo '<pre>',print_r($hotListings),'</pre>';
    }
}
