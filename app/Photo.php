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
        $mlsNumbers = [];

        // Get the listings that still don't have photos 11/6: chunked output to save memory.
        $listingsWithNoPhotos = Listing::where('preferred_image', null)->orWhere('preferred_image', '')->orderBy('id', 'ASC')->get();
        $goodPhotos = 0;
        $missingPhotos = 0;

        foreach ($listingsWithNoPhotos as $listing) { 

            // Try to find photos in the database. If none exist, try to get them from MLS API.
            // If neither of those work, safe to say the the photos haven't been uploaded yet.
            if (Photo::where('listing_id', $listing->id)->exists()) {
                $listing->preferred_image = self::preferredPhotoUrl($listing->id);
                $listing->save();
                $goodPhotos++;
            } else {
                $mlsNumbers[$listing->id] = $listing->mls_account;
                $missingPhotos++;
            }
        }

        echo 'Listings with photos but no preferred photo: ' . $goodPhotos . PHP_EOL;
        echo 'Listings without photos at all: ' . $missingPhotos . PHP_EOL;
        echo '---------------------' . PHP_EOL;

        // Contact RETS to grab all the photos that need updates for all listings at once.
        echo 'Contacting RETS gateway for missing photos' . PHP_EOL;
        $pass = 1;

        foreach(array_chunk($mlsNumbers, 200) as $photoChunk){
            $newPhotos = (new Builder($listing->association))->fetchAllPhotos($photoChunk);
            echo $newPhotos->count() . ' photos received in pass ' . $pass++ . '. Updating...';

            foreach($newPhotos as $photo){
                //if($photo->getContent()){
                    Photo::savePhoto($mlsNumbers, $photo);
                //}
            }
        }

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
