<?php

namespace App\Tests\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use function json_decode;
use function json_encode;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationOk(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['email' => 'test@example.com', 'password' => 'password1'])
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertResponseHasCookie('PHPSESSID');
    }

    public function testRegistrationError(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode(['email' => '', 'password' => 'password1'])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $decoded = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('errors', $decoded);
        self::assertNotEmpty($decoded['errors']);
        self::assertIsArray($decoded['errors'][0]);
        self::assertNotEmpty($decoded['errors'][0]['origin']);
        self::assertNotEmpty($decoded['errors'][0]['message']);
    }
}
