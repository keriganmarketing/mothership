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