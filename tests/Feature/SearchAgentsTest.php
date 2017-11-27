<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchAgentsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function an_agent_is_searchable_by_short_id()
    {
        $agents = factory('App\Agent', 2)->create();

        $response = $this->get($this->baseUri . '/agents?shortId=' . $agents->first()->short_id);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'short_id' => (string) $agents->first()->short_id
        ]);
        $response->assertJsonMissing([
            'short_id' => (string) $agents->find(2)->short_id
        ]);
        $this->assertCount(1, json_decode($response->getContent())->data);
    }

    /** @test */
    public function an_agent_is_searchable_by_full_name()
    {
        $agents = factory('App\Agent', 2)->create();

        $response = $this->get($this->baseUri . '/agents?fullName=' . $agents->first()->full_name);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'full_name' => $agents->first()->full_name
        ]);
        $response->assertJsonMissing([
            'full_name' => $agents->find(2)->full_name
        ]);
        $this->assertCount(1, json_decode($response->getContent())->data);
    }

    /** @test */
    public function an_agent_is_searchable_by_last_name()
    {
        $agents = factory('App\Agent', 2)->create();

        $response = $this->get($this->baseUri . '/agents?lastName=' . $agents->first()->last_name);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'last_name' => $agents->first()->last_name
        ]);
        $response->assertJsonMissing([
            'last_name' => $agents->find(2)->last_name
        ]);
        $this->assertCount(1, json_decode($response->getContent())->data);
    }

    /** @test */
    public function an_agent_is_searchable_by_association()
    {
        $bcar = create('App\Agent', ['association' => 'bcar']);
        $ecar = create('App\Agent', ['association' => 'ecar']);

        $response = $this->get($this->baseUri . '/agents?association=' . $bcar->association);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'association' => 'bcar'
        ]);
        $response->assertJsonMissing([
            'association' => 'ecar'
        ]);
        $this->assertCount(1, json_decode($response->getContent())->data);

    }

    /** @test */
    public function an_agent_is_searchable_by_office_short_id()
    {
        $agents = factory('App\Agent', 2)->create();

        $response = $this->get($this->baseUri . '/agents?officeShortId=' . $agents->first()->office_short_id);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'office_short_id' => (string) $agents->first()->office_short_id
        ]);
        $response->assertJsonMissing([
            'office_short_id' => (string) $agents->find(2)->office_short_id
        ]);
        $this->assertCount(1, json_decode($response->getContent())->data);

    }

    /** @test */
    public function an_agent_is_searchable_by_full_name_even_if_the_full_name_is_not_in_the_database()
    {
        $agent = create('App\Agent',
            [
                'first_name' => 'Bill',
                'last_name'  => 'Thomas',
                'full_name'  => 'William Thomas'
            ]
        );

        $response = $this->get($this->baseUri . '/agents?fullName=Bill Thomas');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $agent->id
        ]);

        $this->assertCount(1, json_decode($response->getContent())->data);

    }
}
