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
     * Persist the photos to the database for a single listing
     *
     * @param Mxed    $listing The listing for the photos
     * @param Array   $photos  The photos for the listing
     * @param Boolean $output  Toggle CLI output
     * @return void
     */
    public static function savePhotos($listingId, $photos, $output = false)
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
                echo ($output ? '|' : null);
            }else{
                $skipped++;
                echo ($output ? 'X' : null);
            }
        }
    }

    /**
     * Persist a single photo to the database
     *
     * @param Array   $listingIds Array[listing->id] => listing->mls_account
     * @param Array   $photo   The photo for the listing
     * @param Boolean $output  Toggle CLI output
     * @return void
     */
    public static function savePhoto($listingIds, $photo, $output = false)
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
            echo ($output ? '|' : null);
        }else{
            echo ($output ? 'X' : null);
        }
    }

    /**
     * Sync preferred photos in the listings table
     * 
     * @param Boolean $output Toggle CLI output
     * @return void
     */
    public static function sync( $output = false )
    {
        $goodPhotos = 0;
        $missingPhotos = 0;

        echo ($output ? '- Syncing Photos -----------' . PHP_EOL : null);

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

        echo ($output ? 'Listings with photos but no preferred photo: ' . $goodPhotos . PHP_EOL : null);
        echo ($output ? 'Listings without photos at all: ' . $missingPhotos . PHP_EOL : null);
        echo ($output ? '----------------------------' . PHP_EOL : null);
    }

    /**
     * Get URL of Preferred Photo by Listing ID
     *
     * @param Mixed $listingId
     * @return \App\Photo 
     */
    protected static function preferredPhotoUrl($listingId)
    {
        if (Photo::where('listing_id', $listingId)->where('preferred', 1)->exists()) {
            return Photo::where('listing_id', $listingId)->where('preferred', 1)->first()->url;
        } else {
            return Photo::where('listing_id', $listingId)->first()->url;
        }
    }

    /**
     * Get Photos using Listing ID
     *
     * @param Mixed $listingId
     * @return \App\Photo 
     */
    public static function fromListingId($listingId)
    {
        return Photo::where('listing_id', $listingId)->get();
    }

    /**
     * Fix Photos that don't have a Listing associated
     *
     * @param Boolean $output Toggle CLI output
     * @return void
     */
    public static function fixListingIds( $output = false )
    {
        echo ($output ? 'starting photo fix' : null);
        $photos = Photo::where('listing_id', '=', 0)->get();
        foreach($photos as $photo){
            $listing = Listing::where('mls_account', $photo->mls_account)->first();
            echo ($output ? $listing->id . PHP_EOL : null);
            Photo::updateOrCreate(
                [
                    'mls_account' => $photo->mls_account,
                    'url'         => $photo->url
                ],
                [
                    'listing_id'  => $listing->id
                ]
            );
        }
    }
}
