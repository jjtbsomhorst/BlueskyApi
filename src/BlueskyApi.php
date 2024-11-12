<?php

namespace cjrasmussen\BlueskyApi;

use cjrasmussen\BlueskyApi\Traits\RepoRequests;
use cjrasmussen\BlueskyApi\Traits\Authentication;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

/**
 * Class for interacting with the Bluesky API/AT protocol
 */
class BlueskyApi
{
    use Authentication;
    use RepoRequests;

	public function __construct(?string $handle = null, ?string $app_password = null, string $api_uri = 'https://bsky.social/xrpc/')
	{
        $this->initClient($api_uri);
        $this->identifier = $handle;
        $this->password = $app_password;
	}

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getAccountDid(): ?string
	{
		if (!isset($this->activeSession)) {
            $this->authenticate();
        }

        return $this->activeSession->did;
	}
}
