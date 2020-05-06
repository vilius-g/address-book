<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Tests\DB\DatabasePrimer;
use function array_map;
use function array_map as array_map1;
use function array_merge;
use function json_decode;
use const JSON_THROW_ON_ERROR;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FullWorkflowTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DatabasePrimer::prime(self::bootKernel());
    }

    public function testMultipleOperations(): void
    {
        $client = self::createClient();

        $user1 = ['email' => 'workflow-user-1@example.com', 'password' => 'password1'];
        $user2 = ['email' => 'workflow-user-2@example.com', 'password' => 'password2'];

        $userId1 = $this->registerUser($client, $user1);
        $userId2 = $this->registerUser($client, $user2);

        $contactId1 = $this->createContact($client, $userId1, ['name' => 'Some person', 'phone' => '+370 600 00001']);
        $contactId2 = $this->createContact(
            $client,
            $userId1,
            ['name' => 'Some other person', 'phone' => '+370 600 00002']
        );

        $this->assertContactIdListEquals($client, [$contactId1, $contactId2]);

        $sharedContactId1 = $this->shareContact($client, $contactId1, $userId2);

        $this->assertSharedContactIdListEquals($client, [$contactId1]);

        $this->loginUser($client, $user2);

        $this->assertContactIdListEquals($client, []);

        $this->assertSharedContactIdListEquals($client, [$contactId1]);

        $this->assertContactAccessible($client, $contactId1);

        $this->deleteSharedContact($client, $sharedContactId1);

        $this->assertContactNotAccessible($client, $contactId1);
    }

    /**
     * @param $userId
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function createContact(Client $client, $userId, array $contact): string
    {
        $client->request(
            'POST',
            '/api/contacts',
            ['json' => array_merge(['owner' => $userId], $contact)]
        );
        self::assertResponseIsSuccessful();

        return $this->decodeJsonResponse($client)['@id'];
    }

    /**
     * @param string[] $contactIdListExpected
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function assertContactIdListEquals(Client $client, array $contactIdListExpected): void
    {
        $client->request('GET', '/api/contacts');
        $decoded = $this->decodeJsonResponse($client);

        $contactIdListActual = array_map(
            static function (array $contact): string {
                return $contact['@id'];
            },
            $decoded['hydra:member']
        );
        self::assertEquals($contactIdListExpected, $contactIdListActual);
    }

    /**
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function extractIdFromResponse(Client $client)
    {
        return $this->decodeJsonResponse($client)['@id'];
    }

    /**
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function decodeJsonResponse(Client $client)
    {
        return json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param $contactId
     * @param $userId2
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function shareContact(Client $client, string $contactId, string $userId2): string
    {
        $client->request(
            'POST',
            '/api/shared_contacts',
            ['json' => ['contact' => $contactId, 'sharedWith' => $userId2]]
        );
        self::assertResponseIsSuccessful();

        return $this->decodeJsonResponse($client)['@id'];
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function loginUser(Client $client, array $credentials): void
    {
        $client->request(
            'POST',
            '/api/auth/login',
            ['json' => $credentials]
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @param string[] $contactIdListExpected
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function assertSharedContactIdListEquals(Client $client, array $contactIdListExpected): void
    {
        $client->request('GET', '/api/shared_contacts');
        $decoded = $this->decodeJsonResponse($client);

        $contactIdListActual = array_map1(
            static function (array $sharedContact): string {
                return $sharedContact['contact']['@id'];
            },
            $decoded['hydra:member']
        );
        self::assertEquals($contactIdListExpected, $contactIdListActual);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function assertContactAccessible(Client $client, string $contactId): void
    {
        $client->request('GET', $contactId);
        self::assertResponseIsSuccessful('Shared item should be accessible.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function deleteSharedContact(Client $client, string $sharedContactId): void
    {
        $client->request('DELETE', $sharedContactId);
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function assertContactNotAccessible(Client $client, string $contactId): void
    {
        $client->request('GET', $contactId);
        self::assertResponseStatusCodeSame(403, 'Un-shared item should no longer be accessible.');
    }

    /**
     * @return mixed
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function registerUser(Client $client, array $arr)
    {
        $client->request(
            'POST',
            '/api/users',
            ['json' => $arr]
        );
        self::assertResponseIsSuccessful();

        return $this->extractIdFromResponse($client);
    }
}
