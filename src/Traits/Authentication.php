<?php

namespace cjrasmussen\BlueskyApi\Traits;

trait Authentication
{
    private ?object $activeSession;

    private ?string $identifier;

    private ?string $password;

    private ?string $accountDid;

    const CREATE_SESSION = 'com.atproto.server.createSession';

    const REFRESH_SESSION = 'com.atproto.server.refreshSession';

    const GET_SESSION = 'com.atproto.server.getSession';

    public function createSession(?string $handle, ?string $password): \stdClass
    {
        $args = [
            'identifier' => $handle,
            'password' => $password,
        ];

        return $this->sendRequest(method: 'POST', lexicon: self::CREATE_SESSION, body: $args);
    }

    public function refreshSession(string $refreshToken): \stdClass
    {
        return $this->sendRequest(method: 'POST', lexicon: self::REFRESH_SESSION, body: '', headers: ['Authorization' => 'Bearer '.$refreshToken]);
    }

    public function getSession(): \stdClass
    {
        return $this->sendRequest('GET', self::GET_SESSION, []);
    }

    public function authenticate(bool $refreshSession = false): void
    {
        if (! isset($this->activeSession)) {
            $this->fromCache();
            if (isset($this->activeSession)) {
                $this->authenticate();
            }

            $sessionData = $this->createSession($this->identifier, $this->password);
            $this->toCache($sessionData);
            $this->activeSession = $sessionData;
        } else {
            try {
                $this->activeSession = $this->refreshSession($this->activeSession->refreshJwt);
                $this->toCache($this->activeSession);
            } catch (\Throwable $t) {
                $this->clearSessionCache();
                $this->activeSession = null;
                $this->authenticate();
            }
        }
    }

    public function getAccountDid(): ?string
    {
        if (! isset($this->activeSession)) {
            $this->authenticate();
        }

        return $this->activeSession->did;
    }
}
