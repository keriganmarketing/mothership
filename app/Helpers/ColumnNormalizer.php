<?php

namespace App\Helpers;

use App\Helpers\Associations;

class ColumnNormalizer
{
    public $acreage;
    public $agent_id;
    public $amenities;
    public $apn;
    public $appliances;
    public $area;
    public $association;
    public $bathrooms;
    public $bedrooms;
    public $city;
    public $class;
    public $colist_agent_id;
    public $colist_office_id;
    public $colisting_member_shortid;
    public $construction;
    public $date_modified;
    public $description;
    public $directions;
    public $elementary_school;
    public $energy_features;
    public $exterior;
    public $full_address;
    public $full_baths;
    public $half_baths;
    public $high_school;
    public $interior;
    public $last_tax_year;
    public $last_taxes;
    public $latitude;
    public $listing_member_shortid;
    public $longitude;
    public $lot_dimensions;
    public $middle_school;
    public $mls_account;
    public $office_id;
    public $price;
    public $property_type;
    public $sqft_source;
    public $sq_ft;
    public $state;
    public $status;
    public $stories;
    public $street_name;
    public $street_number;
    public $street_suffix;
    public $sub_area;
    public $subdivision;
    public $unit_number;
    public $utilities;
    public $waterfront;
    public $waterview_description;
    public $year_built;
    public $zip;
    public $zoning;

    public function __construct($association)
    {
        $this->association = $association;
    }

    public function setColumns($result)
    {
        $this->setStaticColumns($result);
        if ($this->association == Associations::BCAR) {
            $this->setBcarColumns($result);
        }
    }

    public function setStaticColumns($result)
    {
        $this->acreage           = $result['LIST_57'] > 0 ? $result['LIST_57']: 0;
        $this->agent_id          = $result['LIST_5'];
        $this->apn               = $result['LIST_80'];
        $this->area              = preg_replace('/^[0-9]* ?- ?/', '', $result['LIST_29']);
        $this->bathrooms         = isset($result['LIST_67']) ? $result['LIST_67']: 0;
        $this->bedrooms          = isset($result['LIST_66']) ? $result['LIST_66']: 0;
        $this->city              = $result['LIST_39'];
        $this->class             = $result['LIST_8'];
        $this->colist_agent_id   = $result['LIST_6'];
        $this->colist_office_id  = $result['LIST_165'];
        $this->date_modified     = $result['LIST_87'];
        $this->description       = $result['LIST_78'];
        $this->directions        = $result['LIST_82'];
        $this->elementary_school = $result['LIST_88'];
        $this->full_baths        = isset($result['LIST_68']) ? $result['LIST_68']: 0;
        $this->half_baths        = $result['LIST_69'] > 0 ? $result['LIST_69']: 0;
        $this->high_school       = $result['LIST_90'];
        $this->last_taxes        = $result['LIST_75'] > 0 ? $result['LIST_75']: 0;
        $this->last_tax_year     = $result['LIST_76'] > 0 ? $result['LIST_76']: 0;
        $this->latitude          = is_numeric($result['LIST_46']) ? $result['LIST_46']: 0;
        $this->longitude         = is_numeric($result['LIST_47']) ? $result['LIST_47']: 0;
        $this->lot_dimensions    = $result['LIST_56'];
        $this->middle_school     = $result['LIST_89'];
        $this->mls_account       = $result['LIST_3'];
        $this->office_id         = $result['LIST_106'];
        $this->list_date         = $result['LIST_10'];
        $this->sold_date         = $result['LIST_12'];
        $this->sold_price        = is_numeric($result['LIST_23']) ? $result['LIST_23']: 0;
        $this->price             = is_numeric($result['LIST_22']) ? $result['LIST_22']: 0;
        $this->property_type     = $result['LIST_9'];
        $this->sq_ft             = isset($result['LIST_48']) ? $result['LIST_48']: 0;
        $this->sqft_source       = $result['LIST_146'];
        $this->state             = $result['LIST_40'];
        $this->status            = $result['LIST_15'];
        $this->stories           = isset($result['LIST_64']) && is_numeric($result['LIST_64']) ? $result['LIST_64']: 0;
        $this->street_name       = ucwords(strtolower($result['LIST_34']));
        $this->street_number     = $result['LIST_31'];
        $this->street_suffix     = $result['LIST_37'];
        $this->sub_area          = preg_replace('/^[0-9]* ?- ?/', '', $result['LIST_94']);
        $this->subdivision       = $result['LIST_77'];
        $this->unit_number       = $result['LIST_35'];
        $this->waterfront        = $result['LIST_192'];
        $this->year_built        = isset($result['LIST_53']) ? $result['LIST_53']: 0;
        $this->zip               = $result['LIST_43'];
    }

    public function setBcarColumns($result)
    {
        $this->energy_features          = $result['GF20150204172056617468000000'];
        $this->exterior                 = $result['GF20150204172056829043000000'];
        $this->amenities                = $result['GF20150204172056869833000000'];
        $this->appliances               = $result['GF20150204172056907082000000'];
        $this->colisting_member_shortid = $result['colisting_member_shortid'];
        $this->construction             = $result['GF20150204172056790876000000'];
        $this->listing_member_shortid   = $result['listing_member_shortid'];

        if ($this->class == 'A') {
            $this->utilities             = $result['GF20150204172056580165000000'];
            $this->waterview_description = $result['GF20150204172057026304000000'];
            $this->zoning                = $result['GF20150204172056948623000000'];
            $this->interior              = $result['GF20150204172056617468000000'];
        }
        if ($this->class == 'E') {
            $this->waterview_description = $result['GF20150506150258346595000000'];
            $this->utilities             = $result['GF20150506150244143322000000'];
            $this->zoning                = $result['GF20150506150120689916000000'];
            $this->stories               = is_numeric($result['LIST_64']) ? $result['LIST_64']: 0;
        }
        if ($this->class == 'F') {
            $this->waterview_description = $result['GF20150430163750842533000000'];
            $this->utilities             = $result['GF20150430163732973981000000'];
            $this->zoning                = $result['GF20150430163608793806000000'];
            $this->stories               = is_numeric($result['LIST_64']) ? $result['LIST_64']: 0;
        }
        if ($this->class == 'G') {
            $this->interior              = $result['GF20150204172058417689000000'];
            $this->utilities             = $result['GF20150428144746131692000000'];
            $this->zoning                = $result['GF20150204172058725166000000'];
            $this->stories               = is_numeric($result['LIST_64']) ? $result['LIST_64']: 0;
            $this->waterview_description = $result['GF20150204172058691139000000'];
        }
        if ($this->class == 'J') {
            $this->utilities             = $result['GF20150428170540841152000000'];
            $this->zoning                = $result['GF20150428162423317708000000'];
            $this->stories               = is_numeric($result['LIST_64']) ? $result['LIST_64']: 0;
            $this->waterview_description = $result['GF20150204172057327961000000'];
        }

    }

}
