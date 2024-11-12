<?php

namespace Tests\Feature;

use cjrasmussen\BlueskyApi\BlueskyApi;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertNotNull;

class AuthenticationTest extends TestCase
{
    #[Before]
    public function setUp(): void
    {
        $this->client = new BlueskyApi('j.somhorst+dev@gmail.com', 'wvh4-gw2z-xcs3-byb2');
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
