<?php

namespace App;

use App\ApiCall;
use App\Listing;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $guarded = [];

    /**
     * A photo belongs to a listing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listing()
    {
        return $this->belongsTo('App\Listing');
    }

    /**
     * Get all photos for the specified association and persist them to the database
     *
     * @param string $association Can be 'bcar' or 'ecar'
     *
     * @return void
     */
    public function getAllPhotos($association)
    {
        $mls        = new ApiCall();
        $rets       = ($association == 'bcar') ? $mls->loginToBcar() : $mls->loginToEcar();
        $classArray = ($association == 'bcar') ?
            ['A', 'C', 'E', 'F', 'G', 'J'] : ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];

        foreach ($classArray as $class) {
            $this->_downloadPhotos($association, $class, $rets);
        }

        echo 'Adding preferred images to listing database';

        $this->syncPreferredPhotos();

        echo '\nSUCCESS!';
    }

    /**
     * Download photos from the MLS Database
     *
     * @param string         $association Can be 'bcar' or 'ecar'
     * @param string         $class       The class for the listing
     * @param PHRETS\Session $rets        The PHRETS session
     *
     * @return void
     */
    private function _downloadPhotos($association, $class, $rets)
    {
        $listings = Listing::where('class', $class)->where('association', $association)->get();

        foreach ($listings as $listing) {
            $this->_savePhotos($association, $listing, $rets);
        }
    }

    /**
     * Persist the photos to the database
     *
     * @param string   $association Can be 'bcar' or 'ecar'
     * @param string   $listing     The listing for the photos
     * @param \Session $rets        The PHRETS session
     *
     * @return void
     */
    private function _savePhotos($association, $listing, $rets)
    {
        $photos   = $rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);

        foreach ($photos as $photo) {
            Photo::create(
                [
                    'mls_account'       => $listing->mls_account,
                    'url'               => $photo->getLocation(),
                    'preferred'         => $photo->isPreferred(),
                    'listing_id'        => $listing->id,
                    'photo_description' => $photo->getContentDescription()
                ]
            );
        }
    }

    /**
     * Sync preferred photos in the listings table
     *
     * @return void
     */
    public function syncPreferredPhotos()
    {
        $photos    = Photo::where('preferred', 1)->get();
        foreach ($photos as $photo) {
            $listing = Listing::where('id', $photo->listing_id)->first();

            $listing->preferred_image = $photo->url;
            $listing->save();
            echo '.';
        }
    }
}
