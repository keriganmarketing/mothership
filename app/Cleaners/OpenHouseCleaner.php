<?php
namespace App\Cleaners;

use Carbon\Carbon;
use App\OpenHouse;
use App\Cleaners\CleansDatabase;

class OpenHouseCleaner implements CleansDatabase
{
    public function clean()
    {
        $now = Carbon::now();
        $expiredOpenHouses = OpenHouse::where('event_end', '<=', $now)->get();
        foreach ($expiredOpenHouses as $openHouse) {
            $openHouse->delete();
        }
        OpenHouse::syncWithListings();
    }
}