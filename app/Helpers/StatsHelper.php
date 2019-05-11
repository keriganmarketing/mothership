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
use App\Jobs\ProcessListingImpression;

class StatsHelper {

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    protected function isBot()
    {
        if(!preg_match( 
            "/(MSIE|Trident|(?!Gecko.+)Firefox|(?!AppleWebKit.+Chrome.+)Safari(?!.+Edge)|(?!AppleWebKit.+)Chrome(?!.+Edge)|(?!AppleWebKit.+Chrome.+Safari.+)Edge|AppleWebKit(?!.+Chrome|.+Safari)|Gecko(?!.+Firefox))(?: |\/)([\d\.apre]+)|(Symfony)/i", 
            $this->request->header('User-Agent'), 
            $matches )){
            //dd($this->request->header('User-Agent'));
            return true;
        }

        //dd($this->request->header('User-Agent'));
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
        ProcessListingImpression::dispatch($listings, $this->request->header('User-Agent'))->onQueue('default');
    }

    public function logView(Listing $listing)
    {
        if($this->isBot()){
            return false;
        }

        // (new View)->logNew($listing->id);
        ProcessListingView::dispatch($listing, $this->request->header('User-Agent'))->onQueue('default');
    }

    public function logClick(Listing $listing)
    {
        if($this->isBot()){
            return false;
        }

        // (new Click)->logNew($listing->id);
        ProcessListingClick::dispatch($listing, $this->request->header('User-Agent'))->onQueue('default');
    }

    public function logSearch()
    {
        if($this->isBot()){
            return false;
        }

        // ProcessSearch::dispatch($this->request->all())->onQueue('default');
    }
        
}