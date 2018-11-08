<?php
namespace App\Helpers;

use App\Listing;

class ListingsHelper
{
    public static function saveListing($association, $result, $class, $listingId = -1)
    {
        $savedListing = $association == Associations::BCAR ?
            self::updateOrCreateBcarListing($result, $class, $listingId) :
            self::updateOrCreateEcarListing($result, $class, $listingId);

        return $savedListing;
    }

    /**
     * Persist the BCAR listing in storage
     *
     * @param string        $class  The class for the listing
     * @param \Results      $result The PHRETS Results object
     * @return App\Listing  $listing The Listing Object
     */
    private static function updateOrCreateBcarListing($result, $class, $listingId)
    {
        $columns            = new ColumnNormalizer(Associations::BCAR);
        $listingColumnArray = [];

        $columns->setColumns($result);

        foreach ($columns as $key => $value) {
            $listingColumnArray[$key] = $value;
        }

        $listing = Listing::updateOrCreate(
            ['id' => $listingId],
            $listingColumnArray
        );
        $listing->full_address = $listing->buildFullAddress();
        $listing->save();

        return $listing;
    }

    /**
     * Persist the ECAR listing in storage
     *
     * @param object $result The PHRETS listing object
     * @param string $class  The class for the listing
     *
     * @return object $listing The Listing Object
     */
    private static function updateOrCreateEcarListing($result, $class, $listingId)
    {
        $columns            = new ColumnNormalizer(Associations::ECAR);
        $listingColumnArray = [];

        $columns->setColumns($result);

        foreach ($columns as $key => $value) {
            $listingColumnArray[$key] = $value;
        }

        $listing = Listing::updateOrCreate(
            ['id' => $listingId],
            $listingColumnArray
        );
        $listing->full_address = $listing->buildFullAddress();
        $listing->save();

        return $listing;
    }

    /**
     * Persist the ECAR listing in storage
     *
     * @param object $result The PHRETS listing object
     * @param string $class  The class for the listing
     *
     * @return object $listing The Listing Object
     */
    private static function updateOrCreateEcarListingOld($result, $class, $listingId)
    {
        $acreage              = $result['LIST_57'] > 0 ? $result['LIST_57'] : 0;
        $fullBaths            = isset($result['LIST_68']) ? $result['LIST_68'] : 0;
        $propertyType         = $result['LIST_9'] ?? 'Commercial';
        $halfBaths            = $result['LIST_69'] > 0 ? $result['LIST_69'] : 0;
        $lastTaxes            = $result['LIST_75'] > 0 && is_numeric($result['LIST_75']) ? $result['LIST_75'] : 0;
        $lastTaxYear          = $result['LIST_76'] > 0 && is_numeric($result['LIST_76']) && $result['LIST_76'] < 9999 ? $result['LIST_76'] : 0;
        $price                = is_numeric($result['LIST_22']) ? $result['LIST_22'] : 0;
        $latitude             = is_numeric($result['LIST_46']) ? $result['LIST_46'] : 0;
        $longitude            = is_numeric($result['LIST_47']) ? $result['LIST_47'] : 0;
        $sqft                 = isset($result['LIST_48']) && is_numeric($result['LIST_48']) ? $result['LIST_48'] : 0;
        $bedrooms             = isset($result['LIST_66']) && is_numeric($result['LIST_66']) ? $result['LIST_66'] : 0;
        $bathrooms            = isset($result['LIST_67']) && is_numeric($result['LIST_67']) ? $result['LIST_67'] : 0;
        $yearBuilt            = isset($result['LIST_53']) && is_numeric($result['LIST_53']) ? $result['LIST_53'] : 0;
        $stories              = isset($result['LIST_64']) && is_numeric($result['LIST_64']) ? $result['LIST_64'] : 1;
        $zoning               = null;
        $utilities            = null;
        $interior             = null;
        $waterviewDescription = null;
        $pool                 = null;
        $appliances           = null;
        $exterior             = null;
        $energyFeatures       = null;
        $construction         = null;
        if ($class == 'A') {
            $appliances           = $result['GF20131203203523234694000000'];
            $utilities            = $result['GF20131203185458688530000000'];
            $waterviewDescription = $result['GF20131203222538613490000000'];
            $zoning               = $result['GF20131203222306734642000000'];
            $interior             = $result['GF20131203203513863218000000'];
            $pool                 = ($result['LIST_147'] == 'Yes') ? 1 : 0;
            $exterior             = $result['GF20131203203501805928000000'];
            $energyFeatures       = $result['GF20131203185526796706000000'];
            $construction         = $result['GF20131203203446527084000000'];
        }
        if ($class == 'B') {
            $appliances           = $result['GF20131230164912692795000000'];
            $utilities            = $result['GF20131230164915907956000000'];
            $waterviewDescription = $result['GF20131230164916093183000000'];
            $zoning               = $result['GF20131230164916157466000000'];
            $interior             = $result['GF20131230164914843719000000'];
            $pool                 = ($result['LIST_147'] == 'Yes') ? 1 : 0;
            $exterior             = $result['GF20131230164914069211000000'];
            $energyFeatures       = $result['GF20131230164913550188000000'];
            $construction         = $result['GF20131230164913256545000000'];
        }
        if ($class == 'C') {
            $utilities            = $result['GF20131231131427101593000000'];
            $waterviewDescription = $result['GF20131231131427184540000000'];
            $zoning               = $result['GF20131231131427333528000000'];
            $construction         = $result['GF20131231201806058732000000'];
        }
        if ($class == 'E') {
            $waterviewDescription = $result['GF20140103161837200256000000'];
            $zoning               = $result['LIST_74'];
        }
        if ($class == 'F') {
            $waterviewDescription = $result['GF20140106175333111396000000'];
            $zoning               = $result['LIST_74'];
        }
        if ($class == 'G') {
            $appliances           = $result['GF20131230211343236208000000'];
            $interior             = $result['GF20131230211344214865000000'];
            $zoning               = $result['GF20131230211345452659000000'];
            $stories              = is_numeric($result['LIST_64']) ? $result['LIST_64'] : 0;
            $waterviewDescription = $result['GF20131230211345387488000000'];
        }
        if ($class == 'H') {
            $stories              = is_numeric($result['LIST_64']) ? $result['LIST_64'] : 0;
            $waterviewDescription = $result['GF20140122222400891202000000'];
        }

        $listing = Listing::updateOrCreate(
            ['id' => $listingId],
            [
                'mls_account'              => $result['LIST_3'],
                'property_type'            => $propertyType,
                'class'                    => $result['LIST_8'],
                'status'                   => $result['LIST_15'],
                'price'                    => $price,
                'area'                     => preg_replace('/^[0-9]* ?- ?/', '', $result['LIST_29']),
                'street_number'            => $result['LIST_31'],
                'street_name'              => ucwords(strtolower($result['LIST_34'])),
                'unit_number'              => $result['LIST_35'],
                'city'                     => $result['LIST_39'],
                'state'                    => $result['LIST_40'],
                'zip'                      => $result['LIST_43'],
                'latitude'                 => $latitude,
                'longitude'                => $longitude,
                'sq_ft'                    => $sqft,
                'acreage'                  => $acreage,
                'bedrooms'                 => $bedrooms,
                'bathrooms'                => $bathrooms,
                'subdivision'              => $result['LIST_77'],
                'date_modified'            => $result['LIST_87'],
                'sub_area'                 => preg_replace('/^[0-9]* ?- ?/', '', $result['LIST_94']),
                'waterfront'               => $result['LIST_192'],
                'agent_id'                 => $result['LIST_5'],
                'colist_agent_id'          => $result['LIST_6'],
                'office_id'                => $result['LIST_106'],
                'colist_office_id'         => $result['LIST_165'],
                'listing_member_shortid'   => $result['listing_member_shortid'],
                'colisting_member_shortid' => $result['colisting_member_shortid'],
                'pool'                     => $pool,
                'interior'                 => $interior,
                'appliances'               => $appliances,
                'amenities'                => $result['GF20150204172056869833000000'],
                'exterior'                 => $exterior,
                'energy_features'          => $energyFeatures,
                'construction'             => $construction,
                'utilities'                => $utilities,
                'zoning'                   => $zoning,
                'waterview_description'    => $waterviewDescription,
                'elementary_school'        => $result['LIST_88'],
                'middle_school'            => $result['LIST_89'],
                'high_school'              => $result['LIST_90'],
                'sqft_source'              => $result['LIST_146'],
                'year_built'               => $yearBuilt,
                'lot_dimensions'           => $result['LIST_56'],
                'stories'                  => $stories,
                'full_baths'               => $fullBaths,
                'half_baths'               => $halfBaths,
                'last_taxes'               => (int)floor($lastTaxes),
                'last_tax_year'            => $lastTaxYear,
                'description'              => $result['LIST_78'],
                'apn'                      => $result['LIST_80'],
                'directions'               => $result['LIST_82'],
                'association'              => 'ecar',
                'street_suffix'            => $result['LIST_37']

            ]
        );

        $listing->full_address = $listing->buildFullAddress();
        $listing->save();

        return $listing;
    }
}
