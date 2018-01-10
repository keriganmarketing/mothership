<?php
namespace App\Helpers;

use App\Agent;
use App\Photo;
use App\ApiCall;
use App\Listing;
use App\OpenHouse;
use App\AgentPhoto;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Helpers\AgentsHelper;
use App\Helpers\ListingsHelper;
use Illuminate\Support\Facades\DB;

class Builder
{
    protected $association;

    public function __construct($association)
    {
        $this->association = $association;
        $this->mls         = new ApiCall($this->association);
        $this->rets        = $this->mls->login();
        $this->classArray  = $this->association == 'bcar' ?
            ['A', 'C', 'E', 'F', 'G', 'J'] : ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];
    }

    public function freshListings()
    {
        foreach ($this->classArray as $class) {
            $this->fetchListings($class);
        }
    }

    public function freshPhotos()
    {
        foreach ($this->classArray as $class) {
            $listings = Listing::where('class', $class)->where('association', $this->association)->get();
            foreach ($listings as $listing) {
                $photos = $this->fetchPhotos($listing);
                Photo::savePhotos($listing, $photos);
            }
        }

        Photo::syncPreferredPhotos();
    }

    public function freshAgents()
    {
        $maxRowsReached = false;
        $offset = 0;

        while (! $maxRowsReached) {
            $results = $this->rets->Search(
                'ActiveAgent',
                'Agent',
                '*',
                [
                  'Offset' => $offset,
                  'Limit' => 5000,
                  'SELECT' =>
                      'MEMBER_0,MEMBER_1,MEMBER_3,MEMBER_4,MEMBER_5,MEMBER_6,MEMBER_7,MEMBER_8,MEMBER_10,MEMBER_11,MEMBER_12,MEMBER_13,MEMBER_14,MEMBER_15,MEMBER_16,MEMBER_17,MEMBER_18,MEMBER_19,MEMBER_21,STATUS,ACTIVE,MLS_STATUS,LICENSE,TIMESTAMP,OFFICESHORT'
                ]
            );

            foreach ($results as $result) {
                AgentsHelper::updateOrCreateAgent($this->association, $result);
            }

            $offset += $results->getReturnedResultsCount();

            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }
    }

    public function freshAgentPhotos()
    {
        DB::table('agents')->where('association', $this->association)->orderBy('id')->chunk(100, function ($agents) {
            foreach ($agents as $agent) {
                echo '.';
                $this->downloadPhotosForAgent($agent);
            }
        });
    }

    private function downloadPhotosForAgent($agent)
    {
        $photos = $this->rets->GetObject('ActiveAgent', 'Photo', $agent->agent_id, '*', 1);

        foreach ($photos as $photo) {
            if (! $photo->isError()) {
                echo '*';
                AgentPhoto::create([
                    'agent_id'    => $agent->id,
                    'url'         => $photo->getLocation(),
                    'description' => $photo->getContentDescription() ?? 'No Photo description provided'
                ]);
            }
        }
    }

    public function fetchListings($class)
    {
        $offset         = 0;
        $options        = $this->association == 'bcar' ?
            BcarOptions::all($offset) : EcarOptions::all($offset);
        $maxRowsReached = false;

        while (! $maxRowsReached) {
            $results = $this->rets->Search('Property', $class, '*', $options[$class]);

            foreach ($results as $result) {
                ListingsHelper::saveListing($this->association, $result, $class);
            }

            $offset += $results->getReturnedResultsCount();

            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
            }
        }
    }

    public function fetchPhotos($listing)
    {
        $photos = $this->rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);

        return $photos;
    }

    public function openHouses()
    {
        $results = $this->rets->Search('OpenHouse', 'OpenHouse', '*');

        foreach ($results as $result) {
            $listing =  Listing::where('mls_account', $result['LIST105'])->first();
            if ($listing != null) {
                OpenHouse::create([
                    'listing_id'               => $listing->id,
                    'mls_id'                   => $result['LIST105'],
                    'event_unique_id'          => $result['EVENT0'],
                    'last_modified'            => $result['EVENT6'],
                    'event_start'              => $result['EVENT100'],
                    'event_end'                => $result['EVENT200'],
                    'unique_listing_id'        => $result['LIST1'],
                    'list_price'               => $result['LIST22'],
                    'listing_area'             => $result['LIST29'],
                    'street_address'           => $result['ADD0'],
                    'city'                     => $result['ADD5'],
                    'state'                    => $result['ADD10'],
                    'listing_agent_id'         => $result['MBR0'],
                    'listing_agent_first_name' => $result['MBR5'],
                    'listing_agent_last_name'  => $result['MBR7'],
                    'agent_primary_phone'      => $result['PHONE0'],
                    'listing_office_id'        => $result['OFC0'],
                    'listing_office_name'      => $result['OFC3'],
                    'listing_office_phone'     => $result['PHONE1'],
                    'comments'                 => $result['OPEN_HOUSE_COMMENT'],
                ]);
            }
        }
    }
}
