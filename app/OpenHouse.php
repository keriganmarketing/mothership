<?php

namespace App;

use App\Listing;
use Illuminate\Database\Eloquent\Model;

class OpenHouse extends Model
{
    protected $guarded = [];
    protected $table = 'open_houses';

    /**
     * An Open House belongs to a Listing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public function updateFromReturnedResult($result)
    {
        $listing = Listing::where('mls_account', $result['LIST105'])->first();
        if ($listing != null) {
            $this->update([
                'listing_id'               => $listing->id,
                'mls_id'                   => $result['LIST105'],
                'association'              => $this->association,
                'event_unique_id'          => $result['EVENT0'],
                'date_modified'            => $result['EVENT6'],
                'event_start'              => $result['EVENT100'],
                'event_end'                => $result['EVENT200'],
                'unique_listing_id'        => $result['LIST1'],
                'list_price'               => $result['LIST22'],
                'listing_area'             => $result['LIST29'],
                'street_address'           => $result['ADD0'],
                'city'                     => $result['ADD5'],
                'state'                    => $result['ADD10'],
                'listing_agent_id'         => $result['MBR0'],
                'listing_agent_first_name' => $result['MBR5'],
                'listing_agent_last_name'  => $result['MBR7'],
                'agent_primary_phone'      => $result['PHONE0'],
                'listing_office_id'        => $result['OFC0'],
                'listing_office_name'      => $result['OFC3'],
                'listing_office_phone'     => $result['PHONE1'],
                'comments'                 => $result['OPEN_HOUSE_COMMENT'],
            ]);
        }
    }
    public function addEvent($result)
    {
        $listing =  Listing::where('mls_account', $result['LIST105'])->first();
        if ($listing != null) {
            OpenHouse::create([
                'listing_id'               => $listing->id,
                'mls_id'                   => $result['LIST105'],
                'association'              => $listing->association,
                'event_unique_id'          => $result['EVENT0'],
                'date_modified'            => $result['EVENT6'],
                'event_start'              => $result['EVENT100'],
                'event_end'                => $result['EVENT200'],
                'unique_listing_id'        => $result['LIST1'],
                'list_price'               => $result['LIST22'],
                'listing_area'             => $result['LIST29'],
                'street_address'           => $result['ADD0'],
                'city'                     => $result['ADD5'],
                'state'                    => $result['ADD10'],
                'listing_agent_id'         => $result['MBR0'],
                'listing_agent_first_name' => $result['MBR5'],
                'listing_agent_last_name'  => $result['MBR7'],
                'agent_primary_phone'      => $result['PHONE0'],
                'listing_office_id'        => $result['OFC0'],
                'listing_office_name'      => $result['OFC3'],
                'listing_office_phone'     => $result['PHONE1'],
                'comments'                 => $result['OPEN_HOUSE_COMMENT'],
            ]);
        }
    }

    public static function byEventId($eventId)
    {
        return OpenHouse::where('event_unique_id', $eventId)->first();
    }
}
