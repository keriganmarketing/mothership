<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $baseUri;

    protected function setUp()
    {
        parent::setUp();

        $this->baseUri = '/api/v1';
    }
}
