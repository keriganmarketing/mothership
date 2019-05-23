<?php
namespace App\Helpers;

use App\View;
use App\Click;
use App\Search;
use App\Listing;
use Carbon\Carbon;
use App\Impression;
use App\SearchQuery;
use App\Jobs\ProcessSearch;
use App\Jobs\ProcessListingClick;
use App\Jobs\ProcessListingView;
use App\Jobs\ProcessListingImpression;

class StatsHelper {

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function isBot()
    {
        if(preg_match( 
            "/bot|crawl|slurp|spider|mediapartners/i", 
            $this->request->header('Referrer'), 
            $matches )){
            // dd($this->request->header());
            return true;
        }

        // dd($this->request->header());
        return false;
    }

    public function logImpression($listing)
    {
        if($this->isBot()){
            return false;
        }

        (new Impression)->logSingle($listing->id);
    }

    public function logImpressions($listings)
    {
        if($this->isBot()){
            return false;
        }

        // (new Impression)->logMultiple($listings);
        ProcessListingImpression::dispatch($listings, $this->request->header('Origin'))->onQueue('stats');
    }

    public function logView(Listing $listing)
    {
        if($this->isBot()){
            return false;
        }

        // (new View)->logNew($listing->id);
        ProcessListingView::dispatch($listing, $this->request->header('Origin'))->onQueue('stats');
    }

    public function logClick(Listing $listing)
    {
        if($this->isBot()){
            return false;
        }

        // (new Click)->logNew($listing->id);
        ProcessListingClick::dispatch($listing, $this->request->header('Origin'))->onQueue('stats');
    }

    public function logSearch()
    {
        if($this->isBot()){
            return false;
        }

        // (new SearchQuery)->logNew($this->request->all());
        ProcessSearch::dispatch($this->request->all())->onQueue('stats');
    }
        
}