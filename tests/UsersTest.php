<?php

namespace Tests;

class UsersTest extends TestCase
{
    private $faker;
    private $client;
    private $url;
    private $app;

    protected function setUp()
    {
        $this->app = $this->createApplication();
        $this->faker = \Faker\Factory::create();
        $this->client = new \GuzzleHttp\Client();
        $this->url = 'http://localhost:8080'.$this->app['api.endpoint'].'/'.$this->app['api.version'].'/users/';
    }

    public function testUserCreation()
    {
        // Send request without name.
        $response = $this->client->post(
            $this->url,
            ['exceptions' => false]
        );
        $this->assertEquals(400, $response->getStatusCode());

        // Send request with name.
        $response = $this->client->post(
            $this->url,
            ['body' => ['name' => $this->faker->name]]
        );
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testUserRemoval()
    {
        // Send request without id.
        // Throws MethodNotAllowedHttpException exception.
        $response = $this->client->delete(
            $this->url,
            ['exceptions' => false]
        );
        $this->assertEquals(405, $response->getStatusCode());

        // Send request with random id.
        $response = $this->client->delete(
            $this->url.$this->faker->randomNumber,
            ['exceptions' => false]
        );
        $this->assertEquals(404, $response->getStatusCode());
        
        // Send request with max stored id.
        $latest_id = $this->app['db']->fetchColumn('SELECT MAX(id) FROM users');
        $response = $this->client->delete($this->url.$latest_id);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
