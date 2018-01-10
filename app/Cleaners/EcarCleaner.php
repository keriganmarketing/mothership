<?php
namespace App\Cleaners;

use App\Photo;
use App\Listing;
use App\Cleaners\Cleaner;

class EcarCleaner extends Cleaner
{
    protected $association;

    public function __construct()
    {
        $this->association = 'ecar';
        parent::__construct();
    }

    public function clean()
    {
        $listings = Listing::where('association', 'ecar')->pluck('mls_account');
        $listingsArray = [];
        $listingCounter = 0;
        $photoCounter = 0;

        foreach ($this->classArray as $class) {
            $listingsRemain = true;
            $offset = 0;
            while ($listingsRemain) {
                $results = $this->getListingMlsIds($class, $offset);
                foreach ($results as $result) {
                    array_push($listingsArray, $result['LIST_3']);
                }
                $offset += $results->getReturnedResultsCount();
                if ($offset >= $results->getTotalResultsCount()) {
                    $listingsRemain = false;
                }
            }
        }

        $deletedListings = array_diff($listings->toArray(), $listingsArray);

        foreach ($deletedListings as $listing) {
            $fullListing = Listing::where('mls_account', $listing)->first();
            $listingId = $fullListing->id;
            $fullListing->delete();
            $listingCounter = $listingCounter + 1;

            $photos = Photo::where('listing_id', $listingId)->get();
            foreach ($photos as $photo) {
                $photo->delete();
                $photoCounter = $photoCounter + 1;
            }
        }
    }
    protected function getListingMlsIds($class, $offset)
    {
        return $this->rets->Search
            (
                'Property',
                $class,
                '*',
                [
                'Limit' => '99999',
                'Offset' => $offset,
                'Select' => 'LIST_3'
                ]
            );
    }
}
