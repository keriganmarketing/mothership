<?php

namespace App;

use App\ApiCall;
use App\Listing;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Builder;

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
     * Persist the photos to the database
     *
     * @param string   $listing     The listing for the photos
     * @param array    $photos      The photos for the listing
     *
     * @return void
     */
    public function savePhotos($listing, $photos)
    {
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
    public static function syncPreferredPhotos()
    {
        // Photo::where('preferred', 1)->chunk(500, function ($photos){
        //     foreach ($photos as $photo) {
        //         $listing = Listing::where('id', $photo->listing_id)->first();

        //         $listing->preferred_image = $photo->url;
        //         $listing->save();
        //         echo '*';
        //     }
        // });

        $listingsWithNoPhotos = Listing::where('preferred_image', null)->orWhere('preferred_image', '')->get();

        foreach ($listingsWithNoPhotos as $listing) {
            if (Photo::where('listing_id', $listing->id)->exists()) {
                $listing->preferred_image = Photo::where('listing_id', $listing->id)->first()->url;
                $listing->save();
                echo 'z';
            } else {
                $photos = (new Builder($listing->association))->fetchPhotos($listing);
                (new Photo)->savePhotos($listing, $photos);
                echo 'x';
            }
        }
    }
}
