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
        $columns        = new ColumnNormalizer(Associations::BCAR);
        $listingColumns = [];
        $columns->setColumns($result);
        foreach ($columns as $key => $value) {
            $listingColumns[$key] = $value;
        }

        $listing               = Listing::updateOrCreate(['id' => $listingId], $listingColumns);
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
        $columns        = new ColumnNormalizer(Associations::ECAR);
        $listingColumns = [];
        $columns->setColumns($result);
        foreach ($columns as $key => $value) {
            $listingColumns[$key] = $value;
        }

        $listing               = Listing::updateOrCreate(['id' => $listingId], $listingColumns);
        $listing->full_address = $listing->buildFullAddress();
        $listing->save();

        return $listing;

    }
}
