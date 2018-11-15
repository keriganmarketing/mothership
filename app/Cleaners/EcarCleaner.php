<?php
namespace App\Cleaners;

use App\Photo;
use App\Listing;
use App\Cleaners\Cleaner;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Helpers\ListingsHelper;
use Carbon\Carbon;

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
    public function repair()
    {
        foreach ($this->classArray as $class) {
            $offset         = 0;
            $counter        = 0;
            $maxRowsReached = false;
            $oneDayAgo = Carbon::now()->copy()->subDay()->format('Y-m-d') . '+';

            while (! $maxRowsReached) {
                $options = $this->association == 'bcar' ?
                    BcarOptions::all($offset) : EcarOptions::all($offset);
                $results = $this->rets->Search('Property', $class, '(LIST_87='. $oneDayAgo . ')', $options[$class]);
                $totalResultsCount = $results->getReturnedResultsCount();
                echo 'Checking ' . $totalResultsCount . ' results';

                foreach ($results as $result) {
                    echo 'Updating listing ' . ($counter + 1) . ' of ' . $totalResultsCount . ': ';
                    $listing = ListingsHelper::saveListing($this->association, $result, $class);
                    $photos = Photo::where('listing_id', $listing->id)->get();
                    if ($photos->isEmpty()) {
                        $photos = $this->rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);
                        Photo::savePhotos($listing, $photos);
                    }
                    echo PHP_EOL;
                    $counter ++;
                }

                $offset += $results->getReturnedResultsCount();

                if ($offset >= $results->getTotalResultsCount()) {
                    $maxRowsReached = true;
                }
            }
        }
        Photo::sync();
    }
    protected function getListingMlsIds($class, $offset)
    {
        return $this->rets->Search
            (
                'Property',
                $class,
                '(LIST_104=Y)',
                [
                'Limit' => 'None',
                'Offset' => $offset,
                'Select' => 'LIST_3'
                ]
            );
    }
}
