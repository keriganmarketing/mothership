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
                'price'         => '1',
                'status'        => 'Active',
                'property_type' => 'Detached Single Family',
                'bedrooms'      => '1',
                'bathrooms'     => '1',
                'sq_ft'          => '1',
                'acreage'       => '1',
                'pool'          => 0,
                'waterfront'    => 0,
                'mls_account'   => '666666'
            ]
        );
        $this->secondListing = create(
            'App\Listing',
            [
                'city'          => 'Lynn Haven',
                'price'         => '2',
                'status'        => 'Active',
                'property_type' => 'Condominium',
                'bedrooms'      => '2',
                'bathrooms'     => '2',
                'sq_ft'          => '2',
                'acreage'       => '2',
                'pool'          => 1,
                'waterfront'    => 1,
                'mls_account'   => '777777'
            ]
        );
    }

    /** @test */
    public function a_listing_is_searchable_by_city()
    {
        $response = $this->searchFor(['city' => $this->firstListing->city]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['city' => $this->firstListing->city]);
        $response->assertJsonMissing(['city' => $this->secondListing->city]);
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
        $response->assertJsonMissing(['subdivision' => $this->secondListing->subdivision]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_area()
    {
        $response = $this->searchFor(['city' => $this->firstListing->area]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['area' => $this->firstListing->area]);
        $response->assertJsonMissing(['area' => $this->secondListing->area]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_zip()
    {
        $response = $this->searchFor(['city' => $this->firstListing->zip]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['zip' => $this->firstListing->zip]);
        $response->assertJsonMissing(['zip' => $this->secondListing->zip]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_sub_area()
    {
        $response = $this->searchFor(['city' => $this->firstListing->sub_area]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['sub_area' => $this->firstListing->sub_area]);
        $response->assertJsonMissing(['sub_area' => $this->secondListing->sub_area]);
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
        $response->assertJsonMissing(['full_address' => $this->secondListing->full_address]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_mls_account()
    {
        $response = $this->searchFor(['city' => $this->firstListing->mls_account]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['mls_account' => $this->firstListing->mls_account]);
        $response->assertJsonMissing(['mls_account' =>  $this->secondListing->mls_account]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function a_listing_is_searchable_by_min_price()
    {
        $response = $this->searchFor(['minPrice' => 2]);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        $response = $this->searchFor(['minPrice' => 1]);
        $response->assertJsonFragment(['price' => $this->firstListing->price]);
        $response->assertJsonFragment(['price' => $this->secondListing->price]);
        $this->assertCount(2, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 2);
        Queue::assertPushed(ProcessListingImpression::class, 2);
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
        $response->assertJsonFragment(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);
        $this->assertCount(2, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function listings_can_be_searched_by_status()
    {
        $pendingListing = create(
            'App\Listing',
            [
                'city'          => 'Lynn Haven',
                'price'         => '2',
                'status'        => 'Pending',
                'property_type' => 'Condominium',
                'bedrooms'      => '2',
                'bathrooms'     => '2',
                'sq_ft'          => '2',
                'acreage'       => '2',
                'pool'          => 1,
                'waterfront'    => 1,
                'mls_account'   => '777777'
            ]
        );
        $response = $this->searchFor(['status' => 'Active']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $this->firstListing->id]);
        $response->assertJsonMissing(['id' => $pendingListing->id]);
        $this->assertCount(2, json_decode($response->getContent())->data);

        $response = $this->searchFor(['status' => 'Pending']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $pendingListing->id]);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonMissing(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

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
        $response->assertJsonMissing(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);
    }

    /** @test */
    public function listings_can_be_searched_by_bedrooms()
    {
        $response = $this->searchFor(['bedrooms' => 2]);
        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_bathrooms()
    {
        $response = $this->searchFor(['bathrooms' => 2]);
        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_sqft()
    {
        $response = $this->searchFor(['sq_ft' => 2]);
        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_acreage()
    {
        $response = $this->searchFor(['acreage' => 2]);
        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_waterfront()
    {
        $response = $this->searchFor(['waterfront' => 1]);
        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 1);
        Queue::assertPushed(ProcessListingImpression::class, 1);

    }

    /** @test */
    public function listings_can_be_searched_by_pool()
    {
        $response = $this->searchFor(['pool' => 1]);
        $response->assertStatus(200);
        $response->assertJsonMissing(['id' => $this->firstListing->id]);
        $response->assertJsonFragment(['id' => $this->secondListing->id]);
        $this->assertCount(1, json_decode($response->getContent())->data);

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
        $this->assertCount(2, json_decode($response->getContent())->data);

        $response = $this->searchFor(['sortBy' => 'price', 'orderBy' => 'ASC']);

        $sortedListings = json_decode($response->getContent())->data;

        $response->assertStatus(200);
        $this->assertEquals($sortedListings[0]->id, $lowestToHighest[0]);
        $this->assertCount(2, json_decode($response->getContent())->data);

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
        $this->assertEquals($sortedListings[0]->id, $highestToLowest[0]);
        $this->assertCount(2, json_decode($response->getContent())->data);

        $response = $this->searchFor(
            [
                'sortBy' => 'date_modified',
                'orderBy' => 'ASC'
            ]
        );

        $sortedListings = json_decode($response->getContent())->data;

        $response->assertStatus(200);
        $this->assertEquals($sortedListings[0]->id, $lowestToHighest[0]);
        $this->assertCount(2, json_decode($response->getContent())->data);

        Queue::assertPushed(ProcessSearch::class, 2);
        Queue::assertPushed(ProcessListingImpression::class, 2);
    }

    public function searchFor($queries)
    {
        $searchQuery = '?';
        $counter     = 0;
        foreach ($queries as $key => $value) {
            if ($counter > 0) {
                $searchQuery .= '&'. $key .'='. $value;
            } else {
                $searchQuery .= $key . '='. $value;
            }

            $counter++;
        }

        return $this->get($this->baseUri . '/search' . $searchQuery);
    }
}
