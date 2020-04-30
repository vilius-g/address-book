<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckControllerTest extends WebTestCase
{
    public function test(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/healthcheck');

        // Check response status code.
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check response content type is text.
        $this->assertStringStartsWith('text/plain', $client->getResponse()->headers->get('Content-Type'));

        // Check response content.
        $this->assertEquals('OK', $client->getResponse()->getContent());
    }
}
