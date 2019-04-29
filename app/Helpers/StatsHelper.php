<?php
namespace App\Helpers;

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

    protected function isBot()
    {
        if(!preg_match( "/(MSIE|Trident|(?!Gecko.+)Firefox|(?!AppleWebKit.+Chrome.+)Safari(?!.+Edge)|(?!AppleWebKit.+)Chrome(?!.+Edge)|(?!AppleWebKit.+Chrome.+Safari.+)Edge|AppleWebKit(?!.+Chrome|.+Safari)|Gecko(?!.+Firefox))(?: |\/)([\d\.apre]+)|(Symfony)/i", $this->request->header('User-Agent'), $matches )){
            //dd($this->request->header('User-Agent'));
            return true;
        }

        //dd($this->request->header('User-Agent'));
        return false;
    }

    public function logImpression($listings, $request)
    {
        $this->request = $request;

        if($this->isBot()){
            return false;
        }

        ProcessListingImpression::dispatch($listings, $this->request->header('User-Agent'));
    }

    public function logClick($listing, $request)
    {
        $this->request = $request;

        if($this->isBot()){
            return false;
        }

        ProcessListingClick::dispatch($listing, $this->request->header('User-Agent'));
    }

    public function logSearch($request)
    {
        $this->request = $request;

        if($this->isBot()){
            return false;
        }

        ProcessSearch::dispatch($this->request->all());
    }
        
}