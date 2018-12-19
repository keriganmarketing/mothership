<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\ProcessSearch;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessListingImpression;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchListingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();

        Queue::fake();

        // Two separate listings to test the search feature
        $this->firstListing  = create(
            'App\Listing',
            [
                'city'          => 'Panama City',
                'subdivision'   => 'Wild Oaks',
                'full_address'  => '100 1st St.',
                'area'          => 'Area 1',
                'sub_area'      => 'Sub Area 1',
                'zip'           => '11111',
                'price'         => '100000',
                'status'        => 'Active',
                'property_type' => 'Detached Single Family',
                'bedrooms'      => '1',
                'bathrooms'     => '1',
                'sq_ft'         => '1',
                'acreage'       => '1',
                'pool'          => 0,
                'waterfront'    => 0,
                'mls_account'   => '666666',
                'date_modified' => "2018-13-07 09:21:06",
                'class'         => 'A' 
            ]
        );
        $this->secondListing = create(
            'App\Listing',
            [
                'city'          => 'Lynn Haven',
                'subdivision'   => 'Whispering Pines',
                'full_address'  => '200 2nd St.',
                'area'          => 'Area 2',
                'sub_area'      => 'Sub Area 2',
                'zip'           => '22222',
                'price'         => '200000',
                'status'        => 'Active',
                'property_type' => 'Condominium',
                'bedrooms'      => '2',
                'bathrooms'     => '2',
                'sq_ft'         => '2',
                'acreage'       => '2',
                'pool'          => 1,
                'waterfront'    => 1,
                'mls_account'   => '777777',
                'date_modified' => "2018-12-07 09:21:06",
                'class'         => 'A' 
            ]
        );
        $this->thirdListing = create(
            'App\Listing',
            [
                'city'          => 'Southport',
                'subdivision'   => 'Ridgewood',
                'full_address'  => '300 3rd St.',
                'area'          => 'Area 3',
                'sub_area'      => 'Sub Area 3',
                'zip'           => '33333',
                'price'         => '50000',
                'status'        => 'Pending',
                'property_type' => 'Duplex',
                'bedrooms'      => '3',
                'bathrooms'     => '3',
                'sq_ft'         => '3',
                'acreage'       => '3',
                'pool'          => 1,
                'waterfront'    => 0,
                'mls_account'   => '888888',
                'date_modified' => "2018-11-07 09:21:06",
                'class'         => 'A' 
            ]
        );
    }

    /** @test */
    public function a_listing_is_searchable_by_city()
    {
        $response = $this->searchFor(['city' => $this->firstListing->city]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->firstListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_subdivision()
    {
        $response = $this->searchFor(['city' => $this->firstListing->subdivision]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['subdivision' => $this->firstListing->subdivision]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_area()
    {
        $response = $this->searchFor(['city' => $this->firstListing->area]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['area' => $this->firstListing->area]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_zip()
    {
        $response = $this->searchFor(['city' => $this->firstListing->zip]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['zip' => $this->firstListing->zip]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_sub_area()
    {
        $response = $this->searchFor(['city' => $this->secondListing->sub_area]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['sub_area' => $this->secondListing->sub_area]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_full_address()
    {
        $response = $this->searchFor(['city' => $this->firstListing->full_address]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['full_address' => $this->firstListing->full_address]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_mls_account()
    {
        $response = $this->searchFor(['city' => $this->firstListing->mls_account]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->firstListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_min_price()
    {
        $response = $this->searchFor(['minPrice' => $this->secondListing->price]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['price' => $this->secondListing->price]);
        $response->assertJsonMissing(['price' => $this->firstListing->price]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_max_price()
    {
        $response = $this->searchFor(['maxPrice' => $this->firstListing->price]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['price' => $this->firstListing->price]);
        $response->assertJsonMissing(['price' => $this->secondListing->price]);

        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function listings_can_be_searched_by_price_range()
    {
        $response = $this->searchFor(
            [
                'minPrice' => $this->firstListing->price,
                'maxPrice' => $this->secondListing->price
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonFragment(['price' => $this->firstListing->price]);
        $response->assertJsonFragment(['price' => $this->secondListing->price]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function listings_can_be_searched_by_status()
    {
        $response = $this->searchFor(['status' => $this->firstListing->status]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => $this->firstListing->status]);
        $response->assertJsonMissing(['status' => $this->thirdListing->status]);

        $response = $this->searchFor(['status' => $this->thirdListing->status]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => $this->thirdListing->status]);
        $response->assertJsonMissing(['status' => $this->firstListing->status]);

        Queue::assertPushed(ProcessSearch::class, 2);
        Queue::assertPushed(ProcessListingImpression::class, 2);
    }

    /** @test */
    public function listings_can_be_searched_by_property_type()
    {
        $propertyType = implode('|', getPropertyTypes('Single Family Home'));

        $response = $this->searchFor(['propertyType' => $propertyType]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->firstListing->id]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function listings_can_be_searched_by_bedrooms()
    {
        $response = $this->searchFor(['bedrooms' => $this->firstListing->bedrooms]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->firstListing->id]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_bathrooms()
    {
        $response = $this->searchFor(['bathrooms' => $this->firstListing->bathrooms]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['bathrooms' => $this->firstListing->bathrooms]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_sqft()
    {
        $response = $this->searchFor(['sq_ft' => $this->firstListing->sq_ft]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['sq_ft' => $this->firstListing->sq_ft]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_acreage()
    {
        $response = $this->searchFor(['acreage' => $this->firstListing->acreage]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->firstListing->id]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_waterfront()
    {
        $response = $this->searchFor(['waterfront' => $this->firstListing->waterfront]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id']);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_pool()
    {
        $response = $this->searchFor(['pool' => $this->firstListing->pool]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id']);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_sorted_by_price()
    {
        $highestToLowest = \App\Listing::orderBy('price', 'DESC')->pluck('id');
        $lowestToHighest = \App\Listing::orderBy('price', 'ASC')->pluck('id');

        $response = $this->searchFor(
            [
                'sortBy' => 'price',
                'orderBy' => 'DESC'
            ]
        );

        $sortedListings = json_decode($response->getContent())->data;

        $response->assertStatus(200);
        $this->assertEquals($sortedListings[0]->id, $highestToLowest[0]);

        $response = $this->searchFor(['sortBy' => 'price', 'orderBy' => 'ASC']);

        $sortedListings = json_decode($response->getContent())->data;

        $response->assertStatus(200);
        // $this->assertEquals($sortedListings[0]->id, $lowestToHighest[0]);

        Queue::assertPushed(ProcessSearch::class, 2);
        Queue::assertPushed(ProcessListingImpression::class, 2);
    }

    /** @test */
    public function listings_can_be_sorted_by_date_modified()
    {
        $highestToLowest = \App\Listing::orderBy('date_modified', 'DESC')->pluck('id');
        $lowestToHighest = \App\Listing::orderBy('date_modified', 'ASC')->pluck('id');

        $response = $this->searchFor(
            [
                'sortBy' => 'date_modified',
                'orderBy' => 'DESC'
            ]
        );

        $sortedListings = json_decode($response->getContent())->data;

        $response->assertStatus(200);
        // $this->assertEquals($sortedListings[0]->id, $highestToLowest[0]);

        $response = $this->searchFor(
            [
                'sortBy' => 'date_modified',
                'orderBy' => 'ASC'
            ]
        );

        $sortedListings = json_decode($response->getContent())->data;

        $response->assertStatus(200);
        //$this->assertEquals($sortedListings[0]->id, $lowestToHighest[0]);

        Queue::assertPushed(ProcessSearch::class, 2);
        Queue::assertPushed(ProcessListingImpression::class, 2);
    }

    public function searchFor($queries)
    {
        $searchQuery = '';
        $counter     = 0;
        foreach ($queries as $key => $value) {
            $searchQuery .= ($counter == 0 ? '?' : '&') . $key . '='. $value;
            $counter++;
        }

        return $this->get($this->baseUri . '/search' . $searchQuery);
    }
}
