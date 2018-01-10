<?php

namespace App\Updaters;

use App\Agent;
use App\AgentPhoto;
use App\Updaters\Updater;
use App\Helpers\AgentsHelper;
use App\Updaters\MakesUpdates;

class AgentsUpdater extends Updater implements MakesUpdates
{
    protected $association;

    public function __construct($association)
    {
        $this->association = $association;
        parent::__construct();
    }

    public function update()
    {
        $dateLastModified = $this->getLastModifiedDate('agents');

        $results = $this->getNewAgents($dateLastModified);

        foreach ($results as $result) {
            $this->handleResult($result);
        }
    }

    protected function getNewAgents($dateLastModified)
    {
       return $this->rets->Search
       (
            'ActiveAgent',
            'Agent',
            '(TIMESTAMP='. $dateLastModified .'+)',
            [
                'Limit' => 5000,
                'SELECT' => Agent::SEARCH_OPTIONS
            ]
        );
    }

    protected function handleResult($result)
    {
        $mlsId = $result['MEMBER_0'];
        $agent = Agent::byMlsId($mlsId);
        if ($agent == null) {
            $agent = AgentsHelper::updateOrCreateAgent($this->association, $result);
            $photos = $this->getPhotosForAgent($agent);
            AgentPhoto::savePhotos($agent, $photos);
        } else {
            $agent = AgentsHelper::updateOrCreateAgent($this->association, $result);
        }
    }

    protected function getPhotosForAgent($agent)
    {
        return $this->rets->GetObject('ActiveAgent', 'Photo', $agent->agent_id, '*', 1);
    }
}
