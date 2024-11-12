<?php

namespace Tests\Feature;

use cjrasmussen\BlueskyApi\BlueskyApi;
use cjrasmussen\BlueskyApi\Exceptions\ClientException;
use cjrasmussen\BlueskyApi\Utils;
use Faker\Factory;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class RepoTest extends TestCase
{

    private BlueskyApi $client;

    #[Before]
    public function setUp(): void
    {
        $dotEnv = new Dotenv();
        $dotEnv->load(__DIR__ . '/../.env');
        $this->client = new BlueskyApi($_ENV['BLUESKY_USERNAME'], $_ENV['BLUESKY_PASSWORD']);
        $this->faker = Factory::create(['nl_NL']);
    }

    public function testDeleteRecord()
    {
        $this->expectException(ClientException::class);
        $response = $this->client->createPost('Dit is een test', languages: ['nl'], createdAt: null, recordKey: null);
        $recordKey = Utils::getRecordKeyFromRecord($response);
        $this->client->deleteRecord($recordKey);
        $this->client->getRecord($recordKey);
    }

    public function testListRecords()
    {
        self::fail();
    }

    public function testGetRecord()
    {
        self::fail();
    }

    public function testCreateAndDeletePost()
    {
        $response = $this->client->createPost(bodyText: 'Dit is een testje', languages: ['nl'], createdAt: null, recordKey: null);
        self::assertNotNull($response);
        self::assertObjectHasProperty('uri', $response);
        self::assertObjectHasProperty('cid', $response);
        self::assertObjectHasProperty('commit', $response);
        self::assertObjectHasProperty('validationStatus', $response);
        $recordKey = Utils::getRecordKeyFromRecord($response);
        $this->client->deleteRecord($recordKey);
    }

    public function testUploadBlob()
    {
        self::fail();
    }

    public function testRecordUtil()
    {
        $recordUri = 'at://did:plc:t42ay7zpgsyawmcg3vmezkka/app.bsky.feed.post/3laox6pktm426';
        $rKey = Utils::getRecordKeyFromRecordUri($recordUri);
        self::assertEquals($rKey, '3laox6pktm426');
    }
}
