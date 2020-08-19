<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordControllerFunctionalTest extends WebTestCase
{
    public function testCriticalPath(): void
    {
        $client = self::createClient();

        // Get JWT
        $client->request('GET', '/api/login_check/admin');

        $authResponse = $client->getResponse();

        self::assertEquals(200, $authResponse->getStatusCode());

        $client->setServerParameters([
            'HTTP_AUTHORIZATION' => $this->getToken($authResponse->getContent()),
            'CONTENT_TYPE' => 'application/json',
        ]);

        // Create record
        $client->request(
            'POST',
            '/api/records',
            [],
            [],
            [],
            json_encode([
                'title' => 'test album',
                'artist' => ['name' => 'by me'],
                'label' => 'home records',
                'year' => 2020,
                'type' => 'LP'
            ])
        );
        self::assertEquals(201, $client->getResponse()->getStatusCode());
        $record = json_decode($client->getResponse()->getContent());
        self::assertIsNumeric($record->id);
        self::assertIsNumeric($record->artist->id);

        $client->request('GET', '/api/records');

        // Read record
        $client->request('GET', '/api/records/'.$record->id);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        // Update record
        $client->request(
            'PUT',
            '/api/records/'.$record->id,
            [],
            [],
            [],
            json_encode([
                'title' => 'Best album',
                'artist' => ['name' => 'bye me'],
                'label' => 'home records',
                'year' => 2019,
                'type' => 'LP'
            ])
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Read bulk
        $client->request('GET', '/api/records?limit=100&offset=0&year=2019');
        self::assertEquals(200, $client->getResponse()->getStatusCode());
        $newRecord = json_decode($client->getResponse()->getContent())[0];
        self::assertEquals('Best album', $newRecord->title);
        self::assertEquals($record->id, $newRecord->id);
        self::assertNotEquals($record->artist->id, $newRecord->artist->id);

        // Delete record
        $client->request('DELETE', '/api/records/'.$record->id);
        self::assertEquals(204, $client->getResponse()->getStatusCode());
    }

    private function getToken(string $json): string
    {
        $jwt = json_decode($json);

        if (!is_string($jwt)) {
            self::fail("Incorrect token received");
        }

        return sprintf('Bearer %s', $jwt);
    }
}
