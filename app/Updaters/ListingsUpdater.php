<?php

namespace App\Updaters;

use App\Photo;
use App\Listing;
use App\Updaters\Updater;
use App\Updaters\MakesUpdates;
use App\Helpers\ListingsHelper;

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
            $results = $this->getNewProperties($class, $dateLastModified);
            foreach ($results as $result) {
                $this->handleResult($class, $result);
            }
        }
        Photo::sync();
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

    protected function getPhotosForListing($mlsNumber)
    {
        return $this->rets->GetObject('Property', 'HiRes', $mlsNumber, '*', 1);
    }

    protected function delete($photos)
    {
        foreach ($photos as $photo) {
            $photo->delete();
            echo '-';
        }
    }
}
