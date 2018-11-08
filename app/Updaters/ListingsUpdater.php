<?php

namespace App\Updaters;

use App\Photo;
use App\Listing;
use App\Updaters\Updater;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Updaters\MakesUpdates;
use App\Helpers\ListingsHelper;
use Illuminate\Support\Facades\DB;

class ListingsUpdater extends Updater implements MakesUpdates
{
    protected $association;

    public function __construct($association)
    {
        $this->association = $association;
        parent::__construct();
    }

    public function update()
    {
        $dateLastModified = $this->getLastModifiedDate('listings');

        foreach ($this->classArray as $class) {
            echo 'Starting updates for class ' . $class . PHP_EOL;
            $results = $this->getNewProperties($class, $dateLastModified);
            foreach ($results as $result) {
                echo '|';
                $this->handleResult($class, $result);
            }
            echo PHP_EOL . 'done.' . PHP_EOL;
        }

        echo 'Syncing photos.' . PHP_EOL;
        Photo::sync();
        echo 'done.' . PHP_EOL;
    }

    public function troubleshoot($class, $mlsNumber)
    {
        dd($this->rets->Search(
            'Property',
            $class,
            '(LIST_3='. $mlsNumber .')',
            $this->options[$class]
        ));
    }

    public function force($class)
    {
        $offset           = 0;
        $localMlsNumbers  = DB::table('listings')->pluck('mls_account')->toArray();
        $remoteMlsNumbers = [];
        $maxRowsReached   = false;

        while (! $maxRowsReached) {
            $newOptions = $this->association == 'bcar' ?
                BcarOptions::idList($offset) : EcarOptions::idList($offset);

            $results = $this->rets->Search('Property', $class, '*', $newOptions[$class]);
            foreach ($results as $result) {
                $remoteMlsNumbers[] = $result["LIST_3"];
            }

            echo 'Need to update '. count(array_diff($remoteMlsNumbers, $localMlsNumbers)) .' listings.';
            foreach ($results as $result) {
                $this->updateSingle($class, $result);
            }

            $offset += $results->getReturnedResultsCount();

            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }

        Photo::sync();

        echo 'Success';
    }

    protected function updateSingle($class, $result)
    {
        $options = $this->association == 'bcar' ? BcarOptions::class : EcarOptions::class;
        $mlsNumber = $result['LIST_3'];
        $listing = Listing::byMlsNumber($mlsNumber);
        if ($listing == null) {
            $results = $this->rets->Search('Property', $class, '(LIST_3='. $mlsNumber .')', ($options::singleListing())[$class]);
            foreach ($results as $result) {
                $listing = ListingsHelper::saveListing($this->association, $result, $class);
                $photos  = $this->getPhotosForListing($mlsNumber);
                Photo::savePhotos($listing, $photos);
                echo '#'.$mlsNumber . PHP_EOL;
            }
        }
    }

    protected function getNewProperties($class, $dateLastModified)
    {
        return $this->rets->Search(
            'Property',
            $class,
            '(LIST_87=' . $dateLastModified . '+)|(LIST_134=' . $dateLastModified . '+)',
            $this->options[$class]
        );
    }

    protected function handleResult($class, $result)
    {
        $mlsNumber = $result['LIST_3'];
        $listing = Listing::byMlsNumber($mlsNumber);
        if ($listing == null) {
            $listing = ListingsHelper::saveListing($this->association, $result, $class);
            $photos  = $this->getPhotosForListing($mlsNumber);
            Photo::savePhotos($listing, $photos);
        } else {
            $listing = Listing::byMlsNumber($mlsNumber);
            ListingsHelper::saveListing($this->association, $result, $class, $listing->id);
            $oldPhotos  = $listing->photos;
            $this->delete($oldPhotos);
            $newPhotos  = $this->getPhotosForListing($mlsNumber);
            Photo::savePhotos($listing, $newPhotos);
        }
    }

    public function getPhotosForListing($mlsNumber)
    {
        return $this->rets->GetObject('Property', 'HiRes', $mlsNumber, '*', 1);
    }

    public function redownloadPhotos($mlsNumber)
    {
        $listing = Listing::where('mls_account', $mlsNumber)->first();
        $photos  = $this->getPhotosForListing($mlsNumber);

        Photo::savePhotos($listing, $photos);

        echo 'Done';
    }

    protected function delete($photos)
    {
        foreach ($photos as $photo) {
            $photo->delete();
            echo '-';
        }
    }
}
