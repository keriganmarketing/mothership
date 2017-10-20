<?php

namespace App;

use App\ApiCall;
use App\Listing;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo('App\Listing');
    }

    public function getAllPhotos($association)
    {
        $mls        = new ApiCall();
        $rets       = ($association == 'bcar') ? $mls->loginToBcar() : $mls->loginToEcar();
        $classArray = ($association == 'bcar') ? ['A', 'C', 'E', 'F', 'G', 'J'] : ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];

        foreach ($classArray as $class) {
            $this->downloadPhotos($association, $class, $rets);
        }

        echo 'Adding preferred images to listing database';

        $this->syncPreferredPhotos();

        echo '\nSUCCESS!';
    }

    private function downloadPhotos($association, $class, $rets)
    {
        $listings = Listing::where('class', $class)->where('association', $association)->get();

        foreach ($listings as $listing) {
            $this->savePhotos($association, $listing, $rets);
        }
    }
    private function savePhotos($association, $listing, $rets)
    {
        $photos   = $rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);

        foreach ($photos as $photo) {
            Photo::create([
                'mls_account'       => $listing->mls_account,
                'url'               => $photo->getLocation(),
                'preferred'         => $photo->isPreferred(),
                'listing_id'        => $listing->id,
                'photo_description' => $photo->getContentDescription()
            ]);
        }
    }
    public function syncPreferredPhotos()
    {
        $photos    = Photo::where('preferred', 1)->get();
        foreach ($photos as $photo) {
            $listing                  = Listing::where('id', $photo->listing_id)->first();
            $listing->preferred_image = $photo->url;
            $listing->save();
        }
    }
}
