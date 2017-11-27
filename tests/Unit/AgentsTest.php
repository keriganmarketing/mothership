<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class AgentsTest extends TestCase
{
use RefreshDatabase;

    /** @test */
    public function an_agent_has_photos()
    {
        $agent = create('App\Agent');

        $this->assertInstanceOf(Collection::class, $agent->photos);
    }
}
