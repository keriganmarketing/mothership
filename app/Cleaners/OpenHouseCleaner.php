<?php
namespace App\Cleaners;

use Carbon\Carbon;
use App\OpenHouse;
use App\Cleaners\CleansDatabase;

class OpenHouseCleaner implements CleansDatabase
{
    public function clean( $output = false )
    {
        echo ($output ? '--- Cleaning Open Houses ----------' . PHP_EOL : null);

        $now = Carbon::now();
        $expiredOpenHouses = OpenHouse::where('event_end', '<=', $now)->get();
        $openHousesCleaned = 0;
        $listingsUpdated = 0;

        foreach ($expiredOpenHouses as $openHouse) {
            echo ($output ? '|' : null);
            $openHouse->delete();
            $listing = Listing::find($openHouse->listing_id);
            $openHousesCleaned++;

            if ($listing != null) {
                $listing->update([
                    'has_open_houses' => 0
                ]);
                $listingsUpdated++;
            }
        }

        echo ($output ? PHP_EOL . 'Open Houses Deleted: ' . $openHousesCleaned . PHP_EOL : null);
        echo ($output ? 'Listings Updated: ' . $listingsUpdated . PHP_EOL : null);
        OpenHouse::syncWithListings();
        echo ($output ? '--- done ---------------------------' . PHP_EOL : null);
    }
}