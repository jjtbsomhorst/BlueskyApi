<?php

namespace cjrasmussen\BlueskyApi;

use cjrasmussen\BlueskyApi\Traits\RepoRequests;
use cjrasmussen\BlueskyApi\Traits\SessionCacheTrait;

/**
 * Class for interacting with the Bluesky API/AT protocol
 */
class BlueskyApi
{
    use RepoRequests;
    use SessionCacheTrait;

    public function __construct(?string $handle = null, ?string $app_password = null, string $api_uri = 'https://bsky.social/xrpc/')
    {
        $this->initClient($api_uri);
        $this->identifier = $handle;
        $this->password = $app_password;
    }
}
