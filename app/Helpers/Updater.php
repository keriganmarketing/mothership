<?php
namespace App\Helpers;

use App\Agent;
use App\Photo;
use App\ApiCall;
use App\Listing;
use Carbon\Carbon;
use App\AgentPhoto;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Helpers\AgentsHelper;
use App\Helpers\ListingsHelper;

class Updater
{
    protected $association;

    public function __construct($association)
    {
        $this->association = $association;
        $this->mls               = new ApiCall($this->association);
        $this->rets              = $this->mls->login();
        $this->classArray        = $this->association == 'bcar' ?
            ['A', 'C', 'E', 'F', 'G', 'J'] : ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];
    }

    public function updateListings()
    {
        $options = $this->association == 'bcar' ?
            BcarOptions::all() : EcarOptions::all();

        $dateLastModified = Carbon::parse(
            Listing::where('association', $this->association)->pluck('date_modified')->max()
        )->toAtomString();

        foreach ($this->classArray as $class) {
            $results = $this->rets->Search(
                'Property',
                $class,
                '(LIST_87=' . $dateLastModified . '+)',
                $options[$class]
            );

            foreach ($results as $result) {
                $mlsNumber = $result['LIST_3'];

                if (! Listing::where('mls_account', $mlsNumber)->exists()) {
                    $listing = ListingsHelper::saveListing($this->association, $result, $class);
                    $photos  = $this->rets->GetObject('Property', 'HiRes', $mlsNumber, '*', 1);

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
                        echo '.';
                    }
                } else {
                    $listing = Listing::where('mls_account', $result['LIST_3'])->first();
                    ListingsHelper::saveListing($this->association, $result, $class, $listing->id);
                    echo '*';
                }
            }
        }
        Photo::syncPreferredPhotos();
    }

    public function updateAgents()
    {
        $dateLastModified = Carbon::parse(
            Agent::where('association', $this->association)->pluck('date_modified')->max()
        )->toAtomString();

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
}
