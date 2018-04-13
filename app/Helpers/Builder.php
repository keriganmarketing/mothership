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
use Carbon\Carbon;

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

    public function rebuild()
    {
        // $this->freshListings();
        $this->freshPhotos();
        $this->freshAgents();
        $this->freshAgentPhotos();
        $this->openHouses();
    }

    public function freshListings()
    {
        foreach ($this->classArray as $class) {
            echo '---------------------------------------------------------' . PHP_EOL;
            echo 'Downloading Listings for Class ' . $class . ': '. PHP_EOL;
            echo '---------------------------------------------------------' . PHP_EOL;
            $this->fetchListings($class);
        }
    }

    public function freshPhotos()
    {
        foreach ($this->classArray as $class) {
            $listings = DB::table('listings')->where('class', $class)->where('association', $this->association)->orderBy('id', 'asc')->chunk(500, function ($listings) {
                foreach ($listings as $listing) {
                    $photos = $this->fetchPhotos($listing);
                    Photo::savePhotos($listing, $photos);
                }
            });
        }

        Photo::sync();
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
                  'SELECT' => Agent::SEARCH_OPTIONS
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

    /**
     * Build the agents photos table
     *
     * @return void
     */
    public function freshAgentPhotos()
    {
        DB::table('agents')->where('association', $this->association)->orderBy('id')->chunk(100, function ($agents) {
            foreach ($agents as $agent) {
                echo '.';
                $this->downloadPhotosForAgent($agent);
            }
        });
    }

    /**
     * Fetch and save photos for the specified agent
     *
     * @param \App\Agent $agent
     * @return void
     */
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

    /**
     * Fetch listings for the specified class
     *
     * @param string $class
     * @return void
     */
    public function fetchListings($class)
    {
        $offset         = 0;
        $maxRowsReached = false;

        while (! $maxRowsReached) {
            $options        = $this->association == 'bcar' ?
                BcarOptions::all($offset) : EcarOptions::all($offset);
            $results = $this->rets->Search('Property', $class, '*', $options[$class]);

            echo '---------------------------------------------------------' . PHP_EOL;
            echo 'Returned Results: '. $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: '. $results->getTotalResultsCount() . PHP_EOL;
            echo 'Offset before this batch: '. $offset . PHP_EOL;

            foreach ($results as $result) {
                ListingsHelper::saveListing($this->association, $result, $class);
            }

            $offset += $results->getReturnedResultsCount();
            echo 'Offset after this batch: '. $offset . PHP_EOL;

            if ($offset >= $results->getTotalResultsCount()) {

                echo 'Final Offset: '. $offset . PHP_EOL;
                $maxRowsReached = true;
            }
        }
    }

    /**
     * Fetch new photos for the specified listing
     *
     * @param \App\Listing $listing
     * @return \Illuminate\Support\Collection
     */
    public function fetchPhotos($listing)
    {
        $photos = $this->rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);

        return $photos;
    }

    /**
     * Fetch open houses and store them in the database
     *
     * @return void
     */
    public function openHouses()
    {
        $now = Carbon::now()->toAtomString();
        $results = $this->rets->Search('OpenHouse', 'OpenHouse', '(EVENT200='. $now .'+)');
        foreach ($results as $result) {
            (new OpenHouse())->addEvent($result);
        }
        OpenHouse::syncWithListings();
    }
}
