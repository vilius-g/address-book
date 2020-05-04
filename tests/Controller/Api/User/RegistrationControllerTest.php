<?php

namespace App\Tests\Controller\Api\User;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends ApiTestCase
{
    public function testRegistrationOk(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/users',
            ['json' => ['email' => 'test1@example.com', 'password' => 'password1']]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Test that the user has been authenticated.
        $client->request(
            'GET',
            '/api/users/me'
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains(['email' => 'test1@example.com']);
    }

    public function testRegistrationError(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/users',
            ['json' => ['email' => '', 'password' => 'password1']]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
