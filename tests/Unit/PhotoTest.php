<?php

namespace Tests\Feature;

use App\Listing;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_photo_belongs_to_a_listing()
    {
        $photo = create('App\Photo');

        $this->assertInstanceOf(Listing::class, $photo->listing);
    }
}
