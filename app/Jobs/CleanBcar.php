<?php

namespace App\Jobs;

use App\Photo;
use App\ApiCall;
use App\Listing;
use App\Helpers\BcarOptions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CleanBcar implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mls            = new ApiCall('bcar');
        $rets           = $mls->login();
        $bcarOptions    = BcarOptions::all();
        $listings       = Listing::where('association', 'bcar')->pluck('mls_account');
        $listingsArray  = [];
        $listingCounter = 0;
        $photoCounter   = 0;

        $classArray = ['A', 'C', 'E', 'F', 'G', 'J'];

        foreach ($classArray as $class) {
            $results = $rets->Search(
                'Property',
                $class,
                '*',
                [
                'Limit' => '99999',
                'Offset' => 0,
                'Select' => 'LIST_3'
                ]
            );
            foreach ($results as $result) {
                array_push($listingsArray, $result['LIST_3']);
            }
        }
        foreach ($listings as $listing) {
            if (! in_array($listing, $listingsArray)) {
                $fullListing = Listing::where('mls_account', $listing)->first();
                $listingId = $fullListing->id;
                $fullListing->delete();
                $listingCounter = $listingCounter + 1;
                echo '.';

                $photos = Photo::where('listing_id', $listingId)->get();
                foreach ($photos as $photo) {
                    $photo->delete();
                    $photoCounter = $photoCounter +1;
                    echo '*';
                }
            }
        }
        echo "Listings deleted: {$listingCounter}";
        echo "Photos deleted: {$photoCounter}";
    }
}
