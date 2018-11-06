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
use App\Cleaners\BcarCleaner;
use App\Cleaners\EcarCleaner;

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
        $this->freshListings();
        $this->freshPhotos();
        $this->freshAgents();
        $this->freshAgentPhotos();
        $this->openHouses();
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
            echo 'Starting ' . $this->association . ' photos for class ' . $class . PHP_EOL;
            Listing::where('class', $class)->where('association', $this->association)->chunk(500, function ($listings) {
                foreach ($listings as $listing) {
                    $photos = $this->fetchPhotos($listing);
                    Photo::savePhotos($listing, $photos);
                    echo '|';
                }
            });
            echo PHP_EOL;
        }

        echo 'Syncing photos...';
        Photo::sync();
        echo 'done.' . PHP_EOL;
    }

    public function freshAgents()
    {
        $maxRowsReached = false;
        $offset = 0;

        echo 'Adding agents...' . PHP_EOL;

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
                echo '|';
            }

            $offset += $results->getReturnedResultsCount();

            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
                echo PHP_EOL . 'done' . PHP_EOL;
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
        echo 'Adding agent photos...' . PHP_EOL;
        DB::table('agents')->where('association', $this->association)->orderBy('id')->chunk(100, function ($agents) {
            foreach ($agents as $agent) {
                echo '|';
                $this->downloadPhotosForAgent($agent);
            }
        });
        echo PHP_EOL . 'done' . PHP_EOL;
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

        echo 'Adding agent photos for ' . $agent->agent_id . '...' . PHP_EOL;
        foreach ($photos as $photo) {
            if (! $photo->isError()) {
                echo '|';
                AgentPhoto::create([
                    'agent_id'    => $agent->id,
                    'url'         => $photo->getLocation(),
                    'description' => $photo->getContentDescription() ?? 'No Photo description provided'
                ]);
            }
        }
        echo PHP_EOL . 'done' . PHP_EOL;
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
        echo 'Starting ' . $this->association . ', class ' . $class . PHP_EOL;

        while (! $maxRowsReached) {
            $options = $this->association == 'bcar' ?
                BcarOptions::all($offset) : EcarOptions::all($offset);
            $results = $this->rets->Search('Property', $class, '*', $options[$class]);

            foreach ($results as $result) {
                ListingsHelper::saveListing($this->association, $result, $class);
                echo '|';
            }
            echo PHP_EOL;

            $offset += $results->getReturnedResultsCount();
            echo 'current offset: ' . $offset . PHP_EOL;

            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
                echo PHP_EOL . 'done' . PHP_EOL;
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
        echo '.';
        return $photos;
    }

    /**
     * Fetch open houses and store them in the database
     *
     * @return void
     */
    public function openHouses()
    {
        echo 'Syncing open houses... ' . PHP_EOL;
        $now = Carbon::now()->toAtomString();
        $results = $this->rets->Search('OpenHouse', 'OpenHouse', '(EVENT200='. $now .'+)');
        foreach ($results as $result) {
            (new OpenHouse())->addEvent($result);
            echo '|';
        }
        OpenHouse::syncWithListings();

        echo PHP_EOL . 'done' . PHP_EOL;

    }

    public function masterRepair()
    {
        (new EcarCleaner())->repair();
        (new BcarCleaner())->repair();
    }

    public function removeDuplicates()
    {        
        $duplicateRecords = Listing::selectRaw('mls_account, COUNT(mls_account) as occurences')
          ->groupBy('mls_account')
          ->having('occurences', '>', 1)
          ->get();

        if($duplicateRecords->count() > 0){ 

            foreach ($duplicateRecords as $record) {
                Listing::where('mls_account',$record->mls_account)->delete();
                echo '|';
            }

            echo PHP_EOL . 'done... deleted ' . $duplicateRecords->count() . ' records.' . PHP_EOL; 
        }
    }

    public function patchMissingPhotos($class)
    {
        Listing::where('class', $class)->where('association', $this->association)->chunk(500, function ($listings) {
            foreach ($listings as $listing) {
                echo '-- ' . $listing->mls_account . ' ---------';
                if(! Photo::where('mls_account', '=', $listing->mls_account)->exists()) {
                    echo ' nope --' . PHP_EOL;
                    $photos = $this->fetchPhotos($listing);
                    Photo::savePhotos($listing, $photos);
                }else{
                    echo ' ok ----' . PHP_EOL;
                }
            }
        });
    }



}
