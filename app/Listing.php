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
     * Get all BCAR MLS listings
     *
     * @return void
     */
    public function freshBcarListings()
    {
        $classArray = ['A', 'C', 'E', 'F', 'G', 'J'];

        foreach ($classArray as $class) {
            $this->insertBcarListingsIntoDatabase($class);
        }
    }

    /**
     * Get all ECAR MLS listings
     *
     * @return void
     */
    public function freshEcarListings()
    {
        $classArray  = ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];

        foreach ($classArray as $class) {
            $this->insertEcarListingsIntoDatabase($class);
        }
    }

    /**
     * Insert the ECAR listings into the database
     *
     * @param string $class The class of the listing
     *
     * @return void
     */
    public function insertEcarListingsIntoDatabase($class)
    {
        $mls            = new ApiCall();
        $rets           = $mls->loginToEcar();
        $counter        = 0;
        $maxRowsReached = false;
        $offset         = 0;

        echo "Fetching listings for class {$class}:";

        while (! $maxRowsReached) {
            $ecarOptions = EcarOptions::all($offset);

            $results = $rets->Search('Property', $class, '*', $ecarOptions[$class]);

            foreach ($results as $result) {
                ListingsHelper::createEcarListing($result, $class);
            }

            $offset += $results->getReturnedResultsCount();

            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
            echo '\nOffset for class ' . $class . ': ' . $offset .
            ' | Total Results Count:' . $results->getTotalResultsCount() . '\n';
        }
    }

    /**
     * Persist the listings for the specified class into the database
     *
     * @param string $class The class of the listing
     *
     * @return void
     */
    public function insertBcarListingsIntoDatabase($class)
    {
        $mls         = new ApiCall();
        $rets        = $mls->loginToBcar();
        $bcarOptions = BcarOptions::all();

        $results = $rets->Search('Property', $class, '*', $bcarOptions[$class]);

        foreach ($results as $result) {
            ListingsHelper::createBcarListing($result, $class);
        }
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

    /**
     * Update the BCAR listings
     *
     * @return void
     */
    public function updateBcarListings()
    {
        $mls = new ApiCall();
        $rets = $mls->loginToBcar();
        $bcarOptions = BcarOptions::all();

        $classArray = ['A', 'C', 'E', 'F', 'G', 'J'];

        $dateLastModified = Carbon::parse(
            Listing::where('association', 'bcar')->pluck('date_modified')->max()
        )->toAtomString();
        foreach ($classArray as $class) {
            echo '<p>Updating listings for class ' . $class . ':';
            $results = $rets->Search(
                'Property',
                $class,
                '(LIST_87=' . $dateLastModified . '+)',
                $bcarOptions[$class]
            );

            foreach ($results as $result) {
                $mlsNumber = $result['LIST_3'];

                if (! Listing::where('mls_account', $mlsNumber)->exists()) {
                    $listing = ListingsHelper::createBcarListing($result, $class);
                    echo '.';
                    $photos = $rets->GetObject('Property', 'HiRes', $mlsNumber, '*', 1);

                    foreach ($photos as $photo) {
                        Photo::create(
                            [
                            'mls_account'       => $mlsNumber,
                            'url'               => $photo->getLocation(),
                            'preferred'         => $photo->isPreferred(),
                            'listing_id'        => $listing->id,
                            'photo_description' => $photo->getContentDescription()
                            ]
                        );
                    }
                } else {
                    $record = Listing::where('mls_account', $result['LIST_3'])->first();
                    ListingsHelper::updateBcarListings($record, $result, $class);
                    echo '.';
                }
            }
        }
        echo 'Syncing Photos...';

        (new Photo)->syncPreferredPhotos();

        echo '<p>SUCCESS!</p></pre></div>';
    }

    /**
     * Update the ECAR listings
     *
     * @return void
     */
    public function updateEcarListings()
    {
        $mls = new ApiCall();
        $rets = $mls->loginToEcar();
        $ecarOptions = EcarOptions::all();

        $classArray  = ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];

        $dateLastModified = Carbon::parse(
            Listing::where('association', 'ecar')
            ->pluck('date_modified')
            ->max()
        )->toAtomString();
        foreach ($classArray as $class) {
            echo '<p>Updating listings for class ' . $class . ':';
            $results = $rets->Search(
                'Property',
                $class,
                '(LIST_87=' . $dateLastModified . '+)',
                $ecarOptions[$class]
            );

            foreach ($results as $result) {
                $mlsNumber = $result['LIST_3'];

                if (! Listing::where('mls_account', $mlsNumber)->exists()) {
                    $listing = ListingsHelper::createEcarListing($result, $class);
                    echo '.';
                    $photos = $rets->GetObject('Property', 'HiRes', $mlsNumber, '*', 1);

                    foreach ($photos as $photo) {
                        Photo::create(
                            [
                            'mls_account'       => $mlsNumber,
                            'url'               => $photo->getLocation(),
                            'preferred'         => $photo->isPreferred(),
                            'listing_id'        => $listing->id,
                            'photo_description' => $photo->getContentDescription()
                            ]
                        );
                    }
                } else {
                    $record = Listing::where('mls_account', $result['LIST_3'])->first();
                    ListingsHelper::updateEcarListings($record, $result, $class);
                    echo '.';
                }
            }
        }
        echo 'Syncing Photos...';

        (new Photo)->syncPreferredPhotos();

        echo '<p>SUCCESS!</p></pre></div>';
    }

    /**
     * Clean the BCAR listings table
     *
     * @return void
     */
    public function cleanBcar()
    {
        $mls            = new ApiCall();
        $rets           = $mls->loginToBcar();
        $bcarOptions    = BcarOptions::all();
        $listings       = Listing::where('association', 'bcar')->pluck('mls_account');
        $listingsArray  = [];
        $listingCounter = 0;
        $photoCounter   = 0;

        $classArray = ['A', 'C', 'E', 'F', 'G', 'J'];

        foreach ($classArray as $class) {
            $results = $rets->Search(
                'Property',
                $class,
                '*',
                [
                'Limit' => '99999',
                'Offset' => 0,
                'Select' => 'LIST_3'
                ]
            );
            foreach ($results as $result) {
                array_push($listingsArray, $result['LIST_3']);
            }
        }
        foreach ($listings as $listing) {
            if (! in_array($listing, $listingsArray)) {
                $fullListing = Listing::where('mls_account', $listing)->first();
                $listingId = $fullListing->id;
                $fullListing->delete();
                $listingCounter = $listingCounter + 1;
                echo '.';

                $photos = Photo::where('listing_id', $listingId)->get();
                foreach ($photos as $photo) {
                    $photo->delete();
                    $photoCounter = $photoCounter +1;
                    echo '*';
                }
            }
        }
        echo "Listings deleted: {$listingCounter}";
        echo "Photos deleted: {$photoCounter}";
    }

    public static function forAgent($agentShortId)
    {
        if (preg_match('/|/', $agentShortId)) {
            dd('yep');
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
            array_push($hotListings, [$listingId => $clicks]);
        }

        arsort($hotListings);

        echo '<pre>',print_r($hotListings),'</pre>';
    }
}
