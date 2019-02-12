<?php

namespace Crystoline\LaraRestApi\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testStore()
    {
        $response = $this-action('POST', 'Crystoline\LaraRestApiTestController@yourAction', ['links' => 'link1 \n link2']);
        // you can check if response was ok
        $this->assertTrue($response->isOk(), "Custom message if something went wrong");
        // or if view received variable
        $this->assertViewHas('links', ['link1', 'link2']);
    }
}
