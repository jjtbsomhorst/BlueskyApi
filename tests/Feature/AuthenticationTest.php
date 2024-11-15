<?php

namespace Tests\Feature;

use cjrasmussen\BlueskyApi\BlueskyApi;
use Faker\Factory;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

use function PHPUnit\Framework\assertNotNull;

class AuthenticationTest extends TestCase
{
    #[Before]
    public function setUp(): void
    {
        $dotEnv = new Dotenv;
        $dotEnv->load(__DIR__.'/../.env');
        $this->client = new BlueskyApi($_ENV['BLUESKY_USERNAME'], $_ENV['BLUESKY_PASSWORD']);
        $this->faker = Factory::create(['nl_NL']);
    }

    public function testGetAccountDid()
    {
        $did = $this->client->getAccountDid();
        self::assertNotNull($did);
    }

    public function test()
    {
        $session = $this->client->getSession();
        assertNotNull($session);
    }
}
