<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\DB\DatabasePrimer;
use function json_decode;
use const JSON_THROW_ON_ERROR;

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

        // Create first user.
        $client->request(
            'POST',
            '/api/users',
            ['json' => ['email' => 'test3@example.com', 'password' => 'password1']]
        );
        self::assertResponseIsSuccessful();
        $userId1 = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['@id'];

        // Create second user.
        $client->request(
            'POST',
            '/api/users',
            ['json' => ['email' => 'test4@example.com', 'password' => 'password1']]
        );
        self::assertResponseIsSuccessful();
        $userId2 = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['@id'];

        // Create contact #1.
        $client->request(
            'POST',
            '/api/contacts',
            ['json' => ['owner' => $userId1, 'name' => 'Some person', 'phone' => '+370 600 00001']]
        );
        self::assertResponseIsSuccessful();

        // Test contact retrieval.
        $client->request('GET', '/api/contacts');
        $decoded = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(1, $decoded['hydra:totalItems']);

        $contactId = $decoded['hydra:member'][0]['@id'];

        // Create contact #2.
        $client->request(
            'POST',
            '/api/contacts',
            ['json' => ['owner' => $userId1, 'name' => 'Some other person', 'phone' => '+370 600 00002']]
        );

        // Test contact sharing.
        $client->request(
            'POST',
            '/api/shared_contacts',
            ['json' => ['contact' => $contactId, 'sharedWith' => $userId2]]
        );
        self::assertResponseIsSuccessful();

        // Test shared contact list on sending side.
        $client->request('GET', '/api/shared_contacts');
        $decoded = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(1, $decoded['hydra:totalItems']);

        // Login with another user.
        $client->request(
            'POST',
            '/api/login',
            ['json' => ['email' => 'test4@example.com', 'password' => 'password1']]
        );
        self::assertResponseIsSuccessful();

        // Test contact retrieval.
        $client->request('GET', '/api/contacts');
        $decoded = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(0, $decoded['hydra:totalItems']);

        // Test shared contact list on receiving side.
        $client->request('GET', '/api/shared_contacts');
        $decoded = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals(1, $decoded['hydra:totalItems'], 'Receiving user should see shared contacts list.');
        $sharedContactId1 = $decoded['hydra:member'][0]['@id'];
        $contactId1 = $decoded['hydra:member'][0]['contact']['@id'];

        // Test single shared contact retrieval.
        $client->request('GET', $contactId1);
        self::assertResponseIsSuccessful('Shared item should be accessible.');

        // Delete share.
        $client->request('DELETE', $sharedContactId1);
        self::assertResponseIsSuccessful();

        // Test single shared contact retrieval after deletion.
        $client->request('GET', $contactId1);
        self::assertResponseStatusCodeSame(403, 'Un-shared item should no longer be accessible.');
    }
}
