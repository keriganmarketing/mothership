<?php
namespace App\Helpers;

use App\Click;
use App\Listing;
use Carbon\Carbon;
use App\Impression;

class StatsHelper {

    protected $request;

    public function isNotBot($request){
        if(preg_match( "/(MSIE|Trident|(?!Gecko.+)Firefox|(?!AppleWebKit.+Chrome.+)Safari(?!.+Edge)|(?!AppleWebKit.+)Chrome(?!.+Edge)|(?!AppleWebKit.+Chrome.+Safari.+)Edge|AppleWebKit(?!.+Chrome|.+Safari)|Gecko(?!.+Firefox))(?: |\/)([\d\.apre]+)/i", $request->header('User-Agent'), $matches )){
            //dd($request->header('User-Agent'));
            return true;
        }

        return false;
    }
}