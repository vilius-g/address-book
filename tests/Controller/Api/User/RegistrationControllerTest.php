<?php

namespace App\Tests\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationOk(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/user/register',
            ['email' => 'test@example.com', 'password' => 'password1']
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testRegistrationError(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/user/register',
            ['email' => '', 'password' => 'password1']
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
