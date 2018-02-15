<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Listing;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function a_listing_has_photos()
    {
        $listing = create('App\Listing');

        $this->assertInstanceOf(Collection::class, $listing->photos);
    }

    /** @test */
    public function a_listing_has_clicks()
    {
        $listing = create('App\Listing');

        $this->assertInstanceOf(Collection::class, $listing->clicks);
    }

    /** @test */
    public function a_listing_has_impressions()
    {
        $listing = create('App\Listing');

        $this->assertInstanceOf(Collection::class, $listing->impressions);
    }

    /** @test */
    public function a_listing_can_validate_its_address()
    {
        $listing = create('App\Listing', [
            'street_number' => '123',
            'street_name'   => 'Example',
            'street_suffix' => 'Street',
            'city'          => 'Lynn Haven',
        ]);

        $this->assertEquals('123 Example Street, Lynn Haven', $listing->buildFullAddress());
    }

    /** @test */
    public function a_listing_will_not_build_an_invalid_address()
    {
        $listing = create('App\Listing', [
            'street_number' => 'NA',
            'street_name' => 'LOT XXX',
            'city' => 'Lynn Haven',
        ]);

        $this->assertNull($listing->buildFullAddress());
    }

    /** @test */
    public function specified_values_in_listing_columns_can_be_returned_on_command()
    {
        $listing = create('App\Listing');

        $value = $listing->getColumn($listing->city, 'city');

        $this->assertEquals(strtolower($listing->city), $value[0]->city);

    }
}
