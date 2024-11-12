<?php

namespace cjrasmussen\BlueskyApi\Traits;

use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

trait Authentication
{
    private ?object $activeSession;
    private ?string $identifier;
    private ?string $password;
    private ?string $accountDid;

    const CREATE_SESSION = 'com.atproto.server.createSession';
    const REFRESH_SESSION = 'com.atproto.server.refreshSession';
    const GET_SESSION = 'com.atproto.server.getSession';

    /**
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    public function createSession(?string $handle, ?string $password): \stdClass
    {
        $args = [
            'identifier' => $handle,
            'password' => $password,
        ];
        return $this->sendRequest(method: 'POST', lexicon: self::CREATE_SESSION, body: $args);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function refreshSession(string $refreshToken): \stdClass
    {
        return $this->sendRequest('POST', self::REFRESH_SESSION, [], '', null, ['Authorization' => 'Bearer ' . $refreshToken]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getSession(): \stdClass
    {
        $this->authenticate();
        return $this->sendRequest('GET', self::GET_SESSION, []);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function authenticate(bool $refreshSession = false): void
    {
        // check if we have a session
        if (isset($this->activeSession)) {

            // check if that session is valid
            if ($refreshSession) {
                try {
                    $this->activeSession = $this->refreshSession($this->activeSession->refreshJwt);
                } catch (\Throwable $t) {
                    // could not refresh, create a new session
                    $this->activeSession = null;
                    $this->authenticate();
                }
            } else {
                try {
                    $this->getSession();
                    return;
                } catch (\Throwable $t) {
                    $this->authenticate(true);
                }
            }
        }

        $this->activeSession = $this->createSession($this->identifier, $this->password);
    }
}