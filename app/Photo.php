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
    public static function savePhotos($listingId, $photos)
    {
        $updated = 0;
        $skipped = 0;

        foreach ($photos as $photo) {
            $hasUrl = $photo->getLocation() !== null && $photo->getLocation() !== '' ? $photo->getLocation() : false;
            if ($hasUrl) {
                Photo::updateOrCreate(
                    [
                        'mls_account' => $photo->getContentID(),
                        'url'         => $photo->getLocation()
                    ],
                    [
                        'mls_account'       => $photo->getContentID(),
                        'url'               => $photo->getLocation(),
                        'preferred'         => $photo->isPreferred(),
                        'listing_id'        => $listingId,
                        'photo_description' => $photo->getContentDescription()
                    ]
                );
                $updated++;
            }else{
                $skipped++;
            }
        }

        echo $updated . ' updated, ' . $skipped . ' no content or not an image.' . PHP_EOL;
    }

    /**
     * Persist a single photo to the database
     *
     * @param mixed   $listing     The listing for the photo
     * @param array    $photo      The photo for the listing
     *
     * @return void
     */
    public static function savePhoto($listingIds, $photo)
    {
        $hasUrl = $photo->getLocation() !== null && $photo->getLocation() !== '' ? $photo->getLocation() : false;
        if ($hasUrl) {
            Photo::updateOrCreate(
                [
                    'mls_account' => $photo->getContentID(),
                    'url'         => $photo->getLocation()
                ],
                [
                    'mls_account'       => $photo->getContentID(),
                    'url'               => $photo->getLocation(),
                    'preferred'         => $photo->isPreferred(),
                    'listing_id'        => array_search($photo->getContentID(), $listingIds),
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
    public static function sync()
    {
        $goodPhotos = 0;
        $missingPhotos = 0;

        // Get the listings that still don't have photos 11/6: chunked output to save memory.
        Listing::where('preferred_image', null)->orWhere('preferred_image', '')->orderBy('id', 'DESC')->chunk(1500, function($listingsWithNoPhotos)
            use (&$goodPhotos, &$missingPhotos) {

            foreach ($listingsWithNoPhotos as $listing) { 
                // Try to find photos in the database. If none exist, try to get them from MLS API.
                // If neither of those work, safe to say the the photos haven't been uploaded yet.
                if (Photo::where('listing_id', $listing->id)->exists()) {
                    $listing->preferred_image = self::preferredPhotoUrl($listing->id);
                    $listing->save();
                    $goodPhotos++;
                } else {
                    $missingPhotos++;
                }
            }
        });

        echo 'Listings with photos but no preferred photo: ' . $goodPhotos . PHP_EOL;
        echo 'Listings without photos at all: ' . $missingPhotos . PHP_EOL;
        echo '---------------------' . PHP_EOL;

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
