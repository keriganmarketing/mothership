<?php

namespace App;

use App\Click;
use App\Photo;
use App\ApiCall;
use App\Listing;
use Carbon\Carbon;
use App\OpenHouse;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Helpers\ListingsHelper;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessListingImpression;
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

    /**
     * A listing can have many Open Houses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openHouses()
    {
        return $this->hasMany(OpenHouse::class);
    }

    /**
     * A Listing has many clicks
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clicks()
    {
        return $this->hasMany('App\Click', 'listing_id');
    }

    /**
     * A Listing has many impressions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function impressions()
    {
        return $this->hasMany('App\Impression', 'listing_id');
    }

    /**
     * Retrieve the requested listings from the database
     *
     * @param array $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function searchResults($request)
    {
        $city         = $request->city ?? '';
        $status       = $request->status ?? 'Active';
        $propertyType = $request->propertyType ?? '';
        $openHouses   = $request->openHouses ?? '';
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
                $query->whereRaw("city LIKE '%{$city}%'")
                    ->orWhereRaw("zip LIKE '%{$city}%'")
                    ->orWhereRaw("sub_area LIKE '%{$city}%'")
                    ->orWhereRaw("area LIKE '%{$city}%'")
                    ->orWhereRaw("subdivision LIKE '%{$city}%'")
                    ->orWhereRaw("full_address LIKE '%{$city}%'")
                    ->orWhereRaw("mls_account LIKE '%{$city}%'");
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
        ->when($openHouses, function ($query) use ($openHouses) {
            return $query->where('has_open_houses', true);
        })
        ->groupBy('full_address')
        ->orderBy($sortBy, $orderBy)
        ->paginate(36);

        ProcessListingImpression::dispatch($listings);

        // returns paginated links (with GET variables intact!)
        $listings->appends($request->all())->links();

        return $listings;
    }

    /**
     * Return a specific column from the listings database
     *
     * @param string $columnName The name of the column
     *
     * @return array
     */
    public static function getColumn($searchTerm, $columnName)
    {
        $values  = DB::table('listings')
        ->selectRaw("DISTINCT LOWER({$columnName}) as {$columnName}")
        ->whereRaw("LOWER({$columnName}) LIKE LOWER('%{$searchTerm}%')")
        ->get();

        return $values->toArray();
    }

    /**
     * Retrieve listings for the specified agent
     *
     * @param string  $agentShortId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function forAgent($agentShortId)
    {
        if (preg_match('/|/', $agentShortId)) {
            $ids = explode('|', $agentShortId);
        } else {
            $ids = [$agentShortId];
        }
        $listings = Listing::whereIn('listing_member_shortid', $ids)
            ->orWhereIn('colisting_member_shortid', $ids)
            ->orderBy('mls_account', 'asc')
            ->groupBy('full_address')
            ->get();

        ProcessListingImpression::dispatch($listings);

        return $listings;
    }

    /**
     * Build full address from the columns in the database
     *
     * @param \Listing $listing
     *
     * @return mixed|array
     */
    public function buildFullAddress()
    {
        $streetNumber = $this->street_number;
        $streetName   = ucwords(strtolower($this->street_name));
        $city         = ', '. $this->city;
        $fullAddress  = $streetNumber . ' ' . $streetName . $city;

        if ($this->addressIsValid($fullAddress)) {
            return $fullAddress;
        }

        return null;
    }

    /**
     * Check that the given address is valid
     *
     * @param string $fullAddress
     * @return boolean
     */
    public function addressIsValid($fullAddress)
    {
        return preg_match('/^[1-9]+([0-9]*)?\s(\d*?)([A-Z]+)?[a-z].+$/', $fullAddress);
    }

    /**
     * Retrieve the listings for the given MLS numbers
     *
     * @param string  $mlsNumbers
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function findByMlsNumbers($mlsNumbers)
    {
        $mlsArray = explode('|', $mlsNumbers);

        $listings = Listing::whereIn('mls_account', $mlsArray)->get();

        return $listings;
    }

    /**
     * Return listing for the specified MLS number
     *
     * @param string $mlsNumber
     * @return \App\Listing
     */
    public static function byMlsNumber($mlsNumber){
        return Listing::where('mls_account', $mlsNumber)->first();
    }
}
