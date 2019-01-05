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
            echo '---------------------' . PHP_EOL;
            echo 'Starting ' . $this->association . ' photos for class ' . $class . PHP_EOL;
            Listing::where('class', $class)->where('association', $this->association)->chunk(25000, function ($listings) {
                $this->fetchAllPhotos($listings);
            });
            echo PHP_EOL;
        }
    
        echo 'Checking for missing photos...' . PHP_EOL;
        $this->patchMissingPhotos();
        echo 'Syncing preferred photos...';
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
        $pass = 1;
        echo '---------------------' . PHP_EOL;
        echo 'Adding agent photos...' . PHP_EOL;
        
        DB::table('agents')->where('association', $this->association)->orderBy('id')->chunk(75, function ($agents)
            use (&$pass) {
            $agentIds = [];
            foreach ($agents as $agent) { 
                $agentIds[] = $agent->agent_id;
            }

            $photos = $this->rets->GetObject('ActiveAgent', 'Photo', $agentIds, '*', 1);
            echo $photos->count() . ' photos requested in pass ' . $pass++ . '.' . PHP_EOL;

            foreach ($photos as $photo) {
                if (! $photo->isError()) {
                    AgentPhoto::create([
                        'agent_id'    => $agent->id,
                        'url'         => $photo->getLocation(),
                        'description' => $photo->getContentDescription() ?? 'No Photo description provided'
                    ]);
                }
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
        $numListings    = 0;
        $maxRowsReached = false;
        echo 'Starting ' . $this->association . ', class ' . $class . PHP_EOL;
        echo 'current offset: ';
        while (! $maxRowsReached) {
            $options = $this->association == 'bcar' ?
                BcarOptions::all($offset) : EcarOptions::all($offset);
            $results = $this->rets->Search('Property', $class, '*', $options[$class]);

            foreach ($results as $result) {
                ListingsHelper::saveListing($this->association, $result, $class);
                $numListings++;
            }

            $offset += $results->getReturnedResultsCount();
            echo $offset . ' ';

            if ($offset >= $results->getTotalResultsCount()) {
                $maxRowsReached = true;
                echo PHP_EOL . $numListings . ' total added.' . PHP_EOL;
            }
        }
    }

    public function fetchSingleListing($class, $mlsNumber)
    {
        echo 'fetching ' . $mlsNumber . PHP_EOL;
        $options = $this->association == 'bcar' ?
                BcarOptions::all(0) : EcarOptions::all(0);
        $results = $this->rets->Search('Property', $class, '(LIST_3='.$mlsNumber.')', $options[$class]);
        foreach ($results as $result) {
            ListingsHelper::saveListing($this->association, $result, $class);
        }
        echo 'done.' . PHP_EOL;
    }

    /**
     * Fetch new photos for the specified listing
     *
     * @param \App\Listing $listing
     * @return \Illuminate\Support\Collection
     */
    public function fetchPhotos($listing)
    {
        return $this->rets->GetObject('Property', 'HiRes', $listing->mls_account, '*', 1);
    }

    public function fetchPhotoByMls($mls_account)
    {
        $listing = Listing::where('mls_account',$mls_account)->first();
        $photos = $this->rets->GetObject('Property', 'HiRes', $mls_account, '*', 1);
        Photo::savePhotos($listing->id, $photos);
    }

    /**
     * Fetch new photos for all listings in provided array 
     *
     * @param Array of mls numbers
     * @return \Illuminate\Support\Collection
     */
    public function fetchAllPhotos($listings)
    {

        // Required for backward lookup of listing_id in savePhoto()
        $mlsNumbers = [];
        foreach ($listings as $listing) { 
            $mlsNumbers[$listing->id] = $listing->mls_account;
        }

        // Contact RETS to grab all the photos that need updates for all listings at once.
        $pass = 1;

        foreach(array_chunk($mlsNumbers, 200) as $photoChunk){
            $newPhotos = $this->rets->GetObject('Property', 'HiRes', implode(',',$photoChunk), '*', 1);
            echo $newPhotos->count() . ' photos received in pass ' . $pass++ . '.' . PHP_EOL;

            foreach($newPhotos as $photo){
                Photo::savePhoto($mlsNumbers, $photo);
            }
        }

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

    public function patchByCompanyUrl($url)
    {
        $count = 0;
        DB::table('agents')->distinct()->select('office_id')->where('association', $this->association)
            ->where('url','LIKE','%'.$url.'%')->orderBy('id')->chunk(100, function($agents)use($count){
                echo $agents->count() . ' found' . PHP_EOL;
            foreach($agents as $agent){
                $this->patchByOfficeId($agent->office_id);
            }
        });

        Photo::sync();
    }

    public function patchByOfficeId($officeId)
    {
        echo 'Fetching listings by office ' . $officeId . PHP_EOL;
        foreach ($this->classArray as $class) {
            echo 'CLASS ' . $class . PHP_EOL;
            $options = $this->association == 'bcar' ?
                    BcarOptions::all(0) : EcarOptions::all(0);
            $results = $this->rets->Search('Property', $class, '(LIST_106='.$officeId.')', $options[$class]);

            $numListings = 0;
            $mlsNumbers = [];
            foreach ($results as $result) {
                ListingsHelper::saveListing($this->association, $result, $class);
                $numListings++;
                $mlsNumbers[] = $result['LIST_3'];
            }
            echo 'Listings: ' . $numListings . PHP_EOL;

            $listings = Listing::whereIn('mls_account',$mlsNumbers)->get();

            $this->fetchAllPhotos($listings);

            echo '------------------' . PHP_EOL;
        }
    }

    public function forceByMLS($mls)
    {
        echo 'Forcing ' . $mls . PHP_EOL;
        $listings = [];

        foreach ($this->classArray as $class) {
            $options = $this->association == 'bcar' ?
                    BcarOptions::all(0) : EcarOptions::all(0);
            $results = $this->rets->Search('Property', $class, '(LIST_3='.$mls.')', $options[$class]);
            if($results->count() > 0){
                foreach($results as $result){
                    ListingsHelper::saveListing($this->association, $result, $class);
                    echo 'Class: ' . $class . '; Photos: ';
                    $this->fetchPhotoByMls($mls);
                }
            }
        }

        Photo::sync();
        echo '------------------' . PHP_EOL;
    }
}
