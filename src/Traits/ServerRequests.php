<?php

namespace cjrasmussen\BlueskyApi\Traits;

use cjrasmussen\BlueskyApi\Traits\Traits\ClientTrait;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait ServerRequests
{
    use ClientTrait;

    const CREATE_SESSION = 'com.atproto.server.createSession';
    const REFRESH_SESSION = 'com.atproto.server.refreshSession';

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function createSession(?string $handle, ?string $password): ResponseInterface
    {
        $args = [
            'identifier' => $handle,
            'password' => $password,
        ];
        return $this->sendRequest('POST', self::CREATE_SESSION, [], $args, null, []);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function refreshSession(string $refreshToken): ResponseInterface
    {
        return $this->sendRequest('POST', self::REFRESH_SESSION, [], '', null, ['Authorization' => 'Bearer ' . $refreshToken]);
    }
}