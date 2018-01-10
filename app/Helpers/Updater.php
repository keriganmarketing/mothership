<?php
namespace App\Helpers;

use App\Agent;
use App\Photo;
use App\ApiCall;
use App\Listing;
use Carbon\Carbon;
use App\OpenHouse;
use App\AgentPhoto;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Helpers\AgentsHelper;
use App\Helpers\ListingsHelper;
use Illuminate\Support\Facades\DB;

class Updater
{
    protected $association;

    public function __construct($association)
    {
        $this->association = $association;
        $this->mls         = new ApiCall($this->association);
        $this->rets        = $this->mls->login();
        $this->classArray  = $this->association == 'bcar' ?
            ['A', 'C', 'E', 'F', 'G', 'J'] : ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];
        $this->options = $this->association == 'bcar' ?
                            BcarOptions::all() : EcarOptions::all();
    }

    public function updateListings()
    {
        $dateLastModified = $this->getLastModifiedDate('listings');

        foreach ($this->classArray as $class) {
            $results = $this->getNewProperties($class, $dateLastModified);
            foreach ($results as $result) {
                $this->handleResult($class, $result);
            }
        }
        Photo::syncPreferredPhotos();
    }

    public function updateAgents()
    {
        $dateLastModified = $this->getLastModifiedDate('agents');
        $results = $this->rets->Search(
            'ActiveAgent',
            'Agent',
            '(TIMESTAMP='. $dateLastModified .'+)',
            [
              'Limit' => 5000,
              'SELECT' =>
                  'MEMBER_0,MEMBER_1,MEMBER_3,MEMBER_4,MEMBER_5,MEMBER_6,MEMBER_7,MEMBER_8,MEMBER_10,MEMBER_11,MEMBER_12,MEMBER_13,MEMBER_14,MEMBER_15,MEMBER_16,MEMBER_17,MEMBER_18,MEMBER_19,MEMBER_21,STATUS,ACTIVE,MLS_STATUS,LICENSE,TIMESTAMP,OFFICESHORT'
            ]
        );
        foreach ($results as $result) {
            $mlsId = $result['MEMBER_0'];

            if (! Agent::where('agent_id', $mlsId)->exists()) {
                $agent = AgentsHelper::updateOrCreateAgent($this->association, $result);
                $photos = $this->rets->GetObject('ActiveAgent', 'Photo', $agent->agent_id, '*', 1);
                foreach ($photos as $photo) {
                    if (! $photo->isError()) {
                        echo '*';
                        AgentPhoto::create([
                            'agent_id'    => $agent->id,
                            'url'         => $photo->getLocation(),
                            'description' => $photo->getContentDescription() ?? 'No photo description provided'
                        ]);
                    }
                }
            } else {
                $agent = AgentsHelper::updateOrCreateAgent($this->association, $result);
            }
        }
    }

    public function cleanEcar()
    {
        $listings = Listing::where('association', 'ecar')->pluck('mls_account');
        $listingsArray = [];
        $listingCounter = 0;
        $photoCounter = 0;

        foreach ($this->classArray as $class) {
            $listingsRemain = true;
            $offset = 0;
            while ($listingsRemain) {
                $results = $this->rets->Search(
                    'Property',
                    $class,
                    '*',
                    [
                        'Limit' => '10000',
                        'Offset' => $offset,
                        'Select' => 'LIST_3'
                    ]
                );
                foreach ($results as $result) {
                    array_push($listingsArray, $result['LIST_3']);
                }
                $offset += $results->getReturnedResultsCount();
                if ($offset >= $results->getTotalResultsCount()) {
                    $listingsRemain = false;
                }
            }
        }

        $deletedListings = array_diff($listings->toArray(), $listingsArray);

        foreach ($deletedListings as $listing) {
            $fullListing = Listing::where('mls_account', $listing)->first();
            $listingId = $fullListing->id;
            $fullListing->delete();
            $listingCounter = $listingCounter + 1;

            $photos = Photo::where('listing_id', $listingId)->get();
            foreach ($photos as $photo) {
                $photo->delete();
                $photoCounter = $photoCounter + 1;
            }
        }
    }

    public function openHouses()
    {
        $lastModified = $this->getLastModifiedDate('open_houses');
        $results = $this->fetchNewOpenHouses($lastModified);
        foreach ($results as $result) {
            $eventId   = $result['EVENT0'];
            $openHouse = OpenHouse::where('event_unique_id', $eventId)->first();
            if ($openHouse != null) {
                $openHouse->updateFromReturnedResult($result);
            } else {
                (new OpenHouse())->addEvent($result);
            }
        }
    }

    protected function getLastModifiedDate($table)
    {
        $lastModified = Carbon::parse(
                DB::table($table)
                ->where('association', $this->association)
                ->pluck('date_modified')
                ->max()
            )->toAtomString();

        return $lastModified;
    }

    protected function fetchNewOpenHouses($lastModified)
    {
        return $this->rets->Search('OpenHouse', 'OpenHouse', '(EVENT6='. $lastModified .'+)');
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

    protected function getPhotosForListing($mlsNumber)
    {
        return $this->rets->GetObject('Property', 'HiRes', $mlsNumber, '*', 1);
    }
    protected function handleResult($class, $result)
    {
        $mlsNumber = $result['LIST_3'];
        $listing = Listing::byMlsNumber($mlsNumber);
        if (count($listing) == 0) {
            $listing = ListingsHelper::saveListing($this->association, $result, $class);
            $photos  = $this->getPhotosForListing($mlsNumber);

            foreach ($photos as $photo) {
                if (! $photo->isError()) {
                    Photo::create(
                        [
                        'mls_account'       => $mlsNumber,
                        'url'               => $photo->getLocation(),
                        'preferred'         => $photo->isPreferred(),
                        'listing_id'        => $listing->id,
                        'photo_description' => $photo->getContentDescription()
                        ]
                    );
                }
            }
        } else {
            $listing = Listing::where('mls_account', $mlsNumber)->first();
            $oldPhotos  = $listing->photos;
            ListingsHelper::saveListing($this->association, $result, $class, $listing->id);

            foreach ($oldPhotos as $oldPhoto) {
                $oldPhoto->delete();
                echo '-';
            }
            $newPhotos  = $this->rets->GetObject('Property', 'HiRes', $mlsNumber, '*', 1);

            foreach ($newPhotos as $photo) {
                if (! $photo->isError()) {
                    Photo::create(
                        [
                        'mls_account'       => $mlsNumber,
                        'url'               => $photo->getLocation(),
                        'preferred'         => $photo->isPreferred(),
                        'listing_id'        => $listing->id,
                        'photo_description' => $photo->getContentDescription()
                        ]
                    );
                echo '+';
                }
            }
        }
    }
}
