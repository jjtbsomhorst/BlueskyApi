<?php

namespace Feature;

use cjrasmussen\BlueskyApi\BlueskyApi;
use cjrasmussen\BlueskyApi\Exceptions\ClientException;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Dotenv\Dotenv;

use function PHPUnit\Framework\assertNotNull;

class AuthenticationTest extends TestCase
{
    protected BlueskyApi $client;

    private Generator $faker;

    #[Before]
    public function setUp(): void
    {
        $dotEnv = new Dotenv;
        $dotEnv->load(__DIR__.'/../.env');
        $this->client = new BlueskyApi($_ENV['BLUESKY_USERNAME'], $_ENV['BLUESKY_PASSWORD']);
        $this->faker = Factory::create(['nl_NL']);
    }

    #[Test]
    public function getAccountDid()
    {
        $did = $this->client->getAccountDid();
        self::assertNotNull($did);
    }

    #[Test]
    public function invalidAuthentication()
    {
        $this->expectException(ClientException::class);
        $this->client->getSession();
    }

    #[Test]
    public function validateSessionData()
    {
        $this->client->authenticate();
        $session = $this->client->getSession();
        assertNotNull($session);
        self::assertObjectHasProperty('did', $session);
    }

    #[Test]
    public function validateSessionWithCache()
    {
        $salt = $this->faker->text(5);
        $cacheMock = $this->createMock(CacheInterface::class);
        $this->client->setSalt($salt);
        $this->client->setCache($cacheMock);
        $this->client->authenticate();
        $this->client->authenticate(); // check if the cache is hit
        $session = $this->client->getSession();
        assertnotnull($session);
    }
}
