<?php
namespace App\Cleaners;

use App\Photo;
use App\Listing;
use App\Cleaners\Cleaner;

class BcarCleaner extends Cleaner
{
    protected $association;

    public function __construct()
    {
        $this->association = 'bcar';
        parent::__construct();
    }

    public function clean()
    {
        $listingIds     = Listing::where('association', $this->association)->pluck('mls_account');
        $listingsArray  = [];
        $listingCounter = 0;
        $photoCounter   = 0;

        foreach ($this->classArray as $class) {
            $results = $this->getListingMlsIds($class);
            foreach ($results as $result) {
                array_push($listingsArray, $result['LIST_3']);
            }
        }
        foreach ($listingIds as $listingId) {
            if (! in_array($listingId, $listingsArray)) {
                $fullListing = Listing::where('mls_account', $listingId)->first();
                $fullListing->delete();
                $listingCounter = $listingCounter + 1;
                echo '.';

                $photos = Photo::fromListingId($fullListing->id);
                foreach ($photos as $photo) {
                    $photo->delete();
                    $photoCounter = $photoCounter +1;
                    echo '*';
                }
            }
        }
    }

    public function repair()
    {
        foreach ($this->classArray as $class) {
            $offset         = 0;
            $maxRowsReached = false;
            $oneYearAgo = Carbon::now()->copy()->subYear()->format('Y-m-d') . '+';

            while (! $maxRowsReached) {
                $options = $this->association == 'bcar' ?
                    BcarOptions::all($offset) : EcarOptions::all($offset);
                $results = $this->rets->Search('Property', $class, '(LIST_87='. $oneYearAgo . ')', $options[$class]);

                foreach ($results as $result) {
                    $listing = ListingsHelper::saveListing($this->association, $result, $class);
                    $photos = Photo::where('listing_id', $listing->id)->get();
                    if ($photos->isEmpty()) {
                        $photos = $this->rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);
                        Photo::savePhotos($listing, $photos);
                    }
                }

                $offset += $results->getReturnedResultsCount();

                if ($offset >= $results->getTotalResultsCount()) {
                    $maxRowsReached = true;
                }
            }
        }
        Photo::sync();
    }

    protected function getListingMlsIds($class)
    {
        return $this->rets->Search
            (
                'Property',
                $class,
                '*',
                [
                'Limit' => '99999',
                'Offset' => 0,
                'Select' => 'LIST_3'
                ]
            );
    }
}