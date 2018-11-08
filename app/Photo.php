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
     * @param mixed   $listing     The listing for the photos
     * @param array    $photos      The photos for the listing
     *
     * @return void
     */
    public static function savePhotos($listing, $photos)
    {
        foreach ($photos as $photo) {
            $photoLocation = $photo->getLocation();
            $hasUrl = $photoLocation !== null && $photoLocation !== '' ? $photoLocation : false;
            if ($hasUrl) {
                Photo::updateOrCreate(
                    [
                        'mls_account' => $listing->mls_account,
                        'url'         => $photoLocation
                    ],
                    [
                        'mls_account'       => $listing->mls_account,
                        'url'               => $photoLocation,
                        'preferred'         => $photo->isPreferred(),
                        'listing_id'        => $listing->id,
                        'photo_description' => $photo->getContentDescription()
                    ]
                );
            }
            unset($photoLocation);
        }
    }

    /**
     * Sync preferred photos in the listings table
     *
     * @return void
     */
    public static function sync()
    {
        // Get the listings that still don't have photos 11/6: chunked output to save memory.
        Listing::where('preferred_image', null)->orWhere('preferred_image', '')->chunk(500, function ($listingsWithNoPhotos) {
            foreach ($listingsWithNoPhotos as $listing) { 

                // Try to find photos in the database. If none exist, try to get them from MLS API.
                // If neither of those work, safe to say the the photos haven't been uploaded yet.
                if (Photo::where('listing_id', $listing->id)->exists()) {
                    echo $listing->mls_account . ' ---- ok --' . PHP_EOL;
                    $listing->preferred_image = self::preferredPhotoUrl($listing->id);
                    $listing->save();
                    
                } else {
                    echo $listing->mls_account . ' -- nope -- ';
                    $photos = (new Builder($listing->association))->fetchPhotos($listing);
                    echo '.';
                    (new Photo)->savePhotos($listing, $photos);
                    echo '.' . PHP_EOL;
                }
            }
        });
    }

    protected static function preferredPhotoUrl($listingId)
    {
        if (Photo::where('listing_id', $listingId)->where('preferred', 1)->exists()) {
            return Photo::where('listing_id', $listingId)->where('preferred', 1)->first()->url;
        } else {
            return Photo::where('listing_id', $listingId)->first()->url;
        }
    }

    public static function fromListingId($listingId)
    {
        return Photo::where('listing_id', $listingId)->get();
    }
}
