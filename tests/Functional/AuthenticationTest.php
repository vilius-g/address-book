<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Tests\DB\DatabasePrimer;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthenticationTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DatabasePrimer::prime(self::bootKernel());
    }

    /**
     * Test user registration.
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testValidAuthentication(): void
    {
        $client = self::createClient();

        $this->assertUserNotAuthenticated($client);
        $this->authenticateUser($client);
        $this->assertUserAuthenticated($client);
        $this->unauthenticateUser($client);
        $this->assertUserNotAuthenticated($client);
    }

    /**
     * Login user in the system.
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function authenticateUser(Client $client): void
    {
        $client->request(
            'POST',
            '/api/auth/login',
            ['json' => ['email' => 'test-user@example.com', 'password' => 'password123']]
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['email' => 'test-user@example.com']);
    }

    /**
     * Logout user from the system.
     *
     * @throws TransportExceptionInterface
     */
    private function unauthenticateUser(Client $client): void
    {
        $client->request(
            'POST',
            '/api/auth/logout'
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
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
        self::assertJsonContains(['email' => 'test-user@example.com']);
    }

    /**
     * Check that user is not currently authenticated.
     *
     * @throws TransportExceptionInterface
     */
    private function assertUserNotAuthenticated(Client $client): void
    {
        $client->request(
            'GET',
            '/api/users/me'
        );

        self::assertResponseStatusCodeSame(401);
    }

    public function getLoginErrorData(): array
    {
        return [
            'Empty email' => [['email' => '', 'password' => 'password1']],
            'No fields' => [[], 400],
            'Invalid credentials' => [['email' => 'test-user@example.com', 'password' => 'pass']],
        ];
    }

    /**
     * Check that invalid responses result in errors.
     *
     * @dataProvider getLoginErrorData
     *
     * @param array $input      Request content
     * @param int   $statusCode Expected status code
     *
     * @throws TransportExceptionInterface
     */
    public function testInvalidAuthentication(array $input, int $statusCode = 401): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/api/auth/login',
            ['json' => $input]
        );

        self::assertResponseStatusCodeSame($statusCode);
        self::assertResponseHeaderSame('content-type', 'application/json');
    }
}
