<?php
namespace App\Helpers;

use App\Agent;

class AgentsHelper
{
    public static function updateOrCreateAgent($association, $result)
    {
        $agent = Agent::updateOrCreate(
            [
                'agent_id' => $result['MEMBER_0']
            ],
            [
               'agent_id'        => $result['MEMBER_0'],
               'office_id'       => $result['MEMBER_1'],
               'association'     => $association,
               'first_name'      => $result['MEMBER_3'],
               'last_name'       => $result['MEMBER_4'],
               'office_phone'    => $result['MEMBER_5'],
               'cell_phone'      => $result['MEMBER_6'],
               'home_phone'      => $result['MEMBER_7'],
               'fax'             => $result['MEMBER_8'],
               'email'           => $result['MEMBER_10'],
               'url'             => $result['MEMBER_11'],
               'street_1'        => $result['MEMBER_12'],
               'street_2'        => $result['MEMBER_13'],
               'city'            => $result['MEMBER_14'],
               'state'           => $result['MEMBER_15'],
               'zip'             => $result['MEMBER_16'],
               'short_id'        => $result['MEMBER_17'],
               'middle_name'     => $result['MEMBER_18'],
               'full_name'       => $result['MEMBER_19'],
               'primary_phone'   => $result['MEMBER_21'],
               'active_status'   => $result['STATUS'],
               'active'          => $result['ACTIVE'],
               'mls_status'      => $result['MLS_STATUS'],
               'license_number'  => $result['LICENSE'],
               'date_modified'   => $result['TIMESTAMP'],
               'office_short_id' => $result['OFFICESHORT']
            ]
        );
        return $agent;
    }
}
