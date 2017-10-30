<?php

namespace App;

use App\Click;
use App\Photo;
use App\ApiCall;
use App\Listing;
use Carbon\Carbon;
use App\Jobs\ProcessImpression;
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

    public static function searchResults($request)
    {
        $city         = $request->city ?? '';
        $status       = $request->status ?? '';
        $propertyType = $request->propertyType ?? '';
        $minPrice     = $request->minPrice ?? '';
        $maxPrice     = $request->maxPrice ?? '';
        $beds         = $request->bedrooms ?? '';
        $baths        = $request->bathrooms ?? '';
        $sqft         = $request->sq_ft ?? '';
        $acreage      = $request->acreage ?? '';
        $waterfront   = $request->waterfront ?? '';
        $pool         = $request->pool ?? '';
        $sortBy       = $request->sortBy ?? 'date_modified';
        $orderBy      = $request->orderBy ?? 'DESC';

        if ($propertyType != '') {
            $propertyType = explode('|', $propertyType);
        }
        if ($status != '') {
            $status = explode('|', $status);
        }

        $listings = Listing::when($city, function ($query) use ($city) {
            $query->where(function ($query) use ($city) {
                $query->where('city', $city)
                    ->orWhere('zip', $city)
                    ->orWhere('sub_area', $city)
                    ->orWhere('area', $city)
                    ->orWhere('subdivision', $city)
                    ->orWhere('full_address', $city)
                    ->orWhere('mls_account', $city);
            });
        })
        ->when($propertyType, function ($query) use ($propertyType) {
            return $query->whereIn('property_type', $propertyType);
        })
        ->when($status, function ($query) use ($status) {
            return $query->whereIn('status', $status);
        })
        ->when($minPrice, function ($query) use ($minPrice) {
            return $query->where('price', '>=', $minPrice);
        })
        ->when($maxPrice, function ($query) use ($maxPrice) {
            return $query->where('price', '<=', $maxPrice);
        })
        ->when($beds, function ($query) use ($beds) {
            return $query->where('bedrooms', '>=', $beds);
        })
        ->when($baths, function ($query) use ($baths) {
            return $query->where('bathrooms', '>=', $baths);
        })
        ->when($sqft, function ($query) use ($sqft) {
            return $query->where('sq_ft', '>=', $sqft);
        })
        ->when($acreage, function ($query) use ($acreage) {
            return $query->where('acreage', '>=', $acreage);
        })
        ->when($waterfront, function ($query) use ($waterfront) {
            return $query->where('waterfront', true);
        })
        ->when($pool, function ($query) use ($pool) {
            return $query->where('pool', true);
        })
        ->groupBy('full_address')
        ->orderBy($sortBy, $orderBy)
        ->paginate(36);

        ProcessImpression::dispatch($listings);

        // returns paginated links (with GET variables intact!)
        $listings->appends($request->all())->links();

        return $listings;
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
