<?php

namespace App\Updaters;

use App\OpenHouse;
use App\Updaters\Updater;
use App\Updaters\MakesUpdates;

class OpenHousesUpdater extends Updater implements MakesUpdates
{
    protected $association;

    public function __construct($association)
    {
        $this->association = $association;
        parent::__construct();
    }

    public function update($output = false)
    {
        $lastModified = $this->getLastModifiedDate('open_houses');
        $results = $this->getNewOpenHouses($lastModified);
        foreach ($results as $result) {
            echo ($output ? '|' : null);
            $this->handleResult($result);
        }
        OpenHouse::syncWithListings();
    }

    protected function getNewOpenHouses($lastModified)
    {
        return $this->rets->Search('OpenHouse', 'OpenHouse', '(EVENT6='. $lastModified .'+)');
    }

    protected function handleResult($result)
    {
        $eventId   = $result['EVENT0'];
        $openHouse = OpenHouse::byEventId($eventId);
        if ($openHouse != null) {
            $openHouse->updateFromReturnedResult($result);
        } else {
            (new OpenHouse())->addEvent($result);
        }
    }
}
