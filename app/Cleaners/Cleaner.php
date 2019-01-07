<?php
namespace App\Cleaners;


use App\Photo;
use App\ApiCall;
use App\Listing;
use Carbon\Carbon;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Helpers\ListingsHelper;

abstract class Cleaner
{
    protected $mls;
    protected $rets;
    protected $classArray;
    protected $options;

    public function __construct()
    {
        $this->mls         = new ApiCall($this->association);
        $this->rets        = $this->mls->login();
        $this->classArray  = $this->association == 'bcar' ?
            ['A', 'C', 'E', 'F', 'G', 'J'] : ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];
        $this->options = $this->association == 'bcar' ?
                            BcarOptions::all() : EcarOptions::all();
    }

    /*
     * Gets List of MLS Ids from RETS
     * 
     * @param String  $class RETS Class
     * @param Boolean $output Toggle CLI Output
     * @return Array
     */
    public function getListingMlsIds($class, $offset)
    {
        return $this->rets->Search(
            'Property',
            $class,
            '*',
            [
            'Limit' => (isset($this->options[$class]['Limit']) ? $this->options[$class]['Limit'] : 'None'),
            'Offset' => $offset,
            'Select' => 'LIST_3'
            ]
        );
    }

    /*
     * Cleans listings silently removed by RETS
     * 
     * @param Boolean $output Toggle CLI Output
     * @return void
     */
    public function clean( $output = false )
    {
        foreach ($this->classArray as $class) {
            $this->cleanClass($class, $output);
        }
    }

    /*
     * Cleans listings silently removed by RETS by RETS Class
     * 
     * @param String  $class  RETS Class
     * @param Boolean $output Toggle CLI Output
     * @return void
     */
    public function cleanClass( $class, $output = false )
    {
        echo ($output ? '- Class: ' . $class . ' -------------' . PHP_EOL : null);
        echo ($output ? 'Limit: ' .$this->options[$class]['Limit'] . PHP_EOL : null);
        $listings = Listing::where('association', $this->association)->where('class', $class)->pluck('mls_account');
        echo ($output ? 'Local: ' . $listings->count() . PHP_EOL : null);

        $maxRowsReached = false;
        $offset = 0;
        $safe = 0;
        $listingsArray  = [];

        while (!$maxRowsReached) {
            $options = $this->association == 'bcar' ?
                BcarOptions::all($offset) : EcarOptions::all($offset);

            $results = $this->getListingMlsIds($class, $offset);
            if($results->getReturnedResultsCount() == 0){
                echo ($output ? '!! Cancelling job. No results found !!' . PHP_EOL : null);
                return null;
            }

            foreach ($results as $result) {
                array_push($listingsArray, (string) $result['LIST_3']);
                $safe++;
            }
            
            $offset += $results->getReturnedResultsCount();
            $maxRowsReached = ($this->options[$class]['Limit'] > $results->getReturnedResultsCount());
        }
        echo ($output ? 'Remote: ' . $safe . PHP_EOL : null);
        $listings = array_map('strval', $listings->toArray());
        $deletedListings = array_diff($listings, $listingsArray);

        $listingCounter = 0;
        foreach ($deletedListings as $listing) {
            $fullListing = Listing::where('mls_account', $listing)->first();
            $listingId = $fullListing->id;
            $fullListing->delete();
            $listingCounter++;

            $photos = Photo::where('listing_id', $listingId)->get();
            foreach ($photos as $photo) {
                $photo->delete();
            }
        }
        echo ($output ? 'Removed: ' . count($deletedListings) . PHP_EOL : null);
    }

    /*
     * Repairs listings by comparing local and remote datasets
     * 
     * @param Boolean $output Toggle CLI Output
     * @return void
     */
    public function repair( $output = false )
    {
        foreach ($this->classArray as $class) {
           $this->repairClass($class, true, $output);
        }
    }

    /*
     * Repairs listing DATA (no photos) by comparing local and remote datasets
     * 
     * @param Boolean $output Toggle CLI Output
     * @return void
     */
    public function repairData( $output = false )
    {
        foreach ($this->classArray as $class) {
           $this->repairClass($class, false, $output);
        }
    }

    /*
     * Repairs listing Data by comparing local and remote datasets by RETS Class
     * 
     * @param String  $class      RETS Class
     * @param Boolean $photoFixer Toggle Photo Fixer
     * @param Boolean $output     Toggle CLI Output
     * @return void
     */
    public function repairClass( $class, $photoFixer = false, $output = false )
    {
        echo ($output ? '- Class: '.$class.' -------------' . PHP_EOL : null);

        $maxRowsReached = false;
        $offset = 0;

        while (!$maxRowsReached) {
            $options = $this->association == 'bcar' ?
                BcarOptions::all($offset) : EcarOptions::all($offset);

            $results = $this->rets->Search('Property', $class, '*', $options[$class]);

            foreach ($results as $result) {
                echo ($output ? '|' : null);
                $listingExists = Listing::where('mls_account', $result['LIST_3'])->pluck('id');
                $listingId     = ($listingExists->isEmpty() ? -1 : $listingExists[0]);
                $listing       = ListingsHelper::saveListing($this->association, $result, $class, $listingId);

                if($photoFixer) {
                    $photos = Photo::where('listing_id', $listingId)->get();
                    if ($photos->isEmpty()) {
                        $photos = $this->rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);
                        Photo::savePhotos($listingId, $photos, $output);
                    }
                }

            }

            $offset += $results->getReturnedResultsCount();
            $maxRowsReached = ($this->options[$class]['Limit'] > $results->getReturnedResultsCount());
            echo ($output ? ' ' . $offset . ' ' : null);
        }
    }

    /*
     * Removed duplicate listings by MLS number
     * 
     * @param Boolean $output Toggle CLI Output
     * @return void
     */
    public function removeDuplicates( $output = false )
    {     
        echo ($output ? '- Removing Duplicates ------' . PHP_EOL : null);

        $duplicateRecords = Listing::selectRaw('mls_account, COUNT(mls_account) as occurences')
          ->groupBy('mls_account')
          ->having('occurences', '>', 1)
          ->get();

        echo ($output ? 'Found: ' . $duplicateRecords->count() . PHP_EOL : null);

        if($duplicateRecords->count() > 0){ 
            foreach ($duplicateRecords as $record) {
                $listings = Listing::where('mls_account', $record->mls_account)->get();
                $listings->forget(0);
                echo ($output ? '|' : null);
                
                foreach($listings as $toDelete){
                    Listing::where('id', $toDelete->id)->delete(); 
                    Photo::where('listing_id', $toDelete->id)->delete(); 
                }
            }          

            echo ($output ? PHP_EOL . 'done. ' . $listings->count() . ' records remved.' . PHP_EOL : null);
        }
    }

    /*
     * Adds photos to listings that don't have any
     * 
     * @param Boolean $output Toggle CLI Output
     * @return void
     */
    public function patchMissingPhotos( $output = false )
    {
        echo ($output ? '- Patching photos ----------' . PHP_EOL : null);
        foreach ($this->classArray as $class) {

            $numGood = 0;
            $numBad = 0;
            $listingsToUpdate = [];

            echo ($output ? 'Class: ' . $class . PHP_EOL : null);
            Listing::where('class', $class)->where('association', $this->association)->chunk(2000, function ($listings)
                use (&$numGood, &$numBad, &$listingsToUpdate, &$output) {
                foreach ($listings as $listing) {
                    if(! Photo::where('listing_id', '=', $listing->id)->exists()) {
                        $listingsToUpdate[$listing->id] = $listing->mls_account;
                        $numBad++;
                        // echo ($output ? '0' : null);
                    }else{
                        $numGood++;
                        // echo ($output ? '|' : null);
                    }
                }
                echo ($output ? '|' : null);
            });

            echo ($output ? PHP_EOL .'Listings With Photos: ' . $numGood . PHP_EOL : null);
            echo ($output ? 'Listings Missing Photos: ' . $numBad . PHP_EOL : null);

            // Contact RETS to grab all the photos that need updates for all listings at once.
            $photoCounter = 0;
            foreach(array_chunk($listingsToUpdate, 200) as $photoChunk){
                $newPhotos = $this->rets->GetObject('Property', 'HiRes', implode(',',$photoChunk), '*', 1);
                foreach($newPhotos as $photo){
                    Photo::savePhoto($listingsToUpdate, $photo);
                    $photoCounter++;
                }
            }

            echo ($output ? 'Total Photos Added: ' . $photoCounter . PHP_EOL: null);
            echo ($output ? '--------------------------' . PHP_EOL : null);
        }
    }

}