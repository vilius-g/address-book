<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Tests\DB\DatabasePrimer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RegistrationTest extends ApiTestCase
{
    /**
     * Test user registration.
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRegistrationOk(): void
    {
        $client = self::createClient();
        DatabasePrimer::prime(self::$kernel);

        $this->registerUser($client);
        $this->assertUserAuthenticated($client);
    }

    /**
     * Register user in the system.
     *
     * @throws TransportExceptionInterface
     */
    private function registerUser(Client $client): void
    {
        $client->request(
            'POST',
            '/api/users',
            ['json' => ['email' => 'test1@example.com', 'password' => 'password1']]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    /**
     * Check that user is currently authenticated.
     *
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    private function assertUserAuthenticated(Client $client): void
    {
        $client->request(
            'GET',
            '/api/users/me'
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(['email' => 'test1@example.com']);
    }

    public function getRegistrationErrorData(): array
    {
        return [
            'Empty email' => [['email' => '', 'password' => 'password1']],
            'No fields' => [[]],
            'Already registered' => [['email' => 'test-user@example.com', 'password' => 'pass']],
        ];
    }

    /**
     * Check that invalid responses result in errors.
     *
     * @dataProvider getRegistrationErrorData
     *
     * @param array $input Request content
     *
     * @throws TransportExceptionInterface
     */
    public function testRegistrationError(array $input): void
    {
        $client = self::createClient();
        DatabasePrimer::prime(self::$kernel);

        $client->request(
            'POST',
            '/api/users',
            ['json' => $input]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
