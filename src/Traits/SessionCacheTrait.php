<?php

namespace cjrasmussen\BlueskyApi\Traits;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

trait SessionCacheTrait
{
    use Authentication;

    protected const INITIALIZATION_VECTOR = 'wj0y79an245dqx90obkh86w8y0gg9k7y';

    protected string $salt = 'm5uaw8n5y7lnbe1xfefdmdlfby6fsb7e';

    protected const ENCRYPTION_METHOD = 'AES-256-CBC';

    protected CacheInterface $cache;

    public function setSalt(string $salt): self
    {
        $this->clearSessionCache();
        $this->salt = $salt;

        return $this;
    }

    private function getCacheKey(): string
    {
        return $this->identifier.':'.$this->salt;
    }

    public function setCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function fromCache(): void
    {
        if (! isset($this->cache)) {
            return;
        }

        try {
            $value = $this->cache->get($this->getCacheKey(), null);
            if (isset($value)) {
                $data = openssl_decrypt($value, self::ENCRYPTION_METHOD, $this->salt, 0, self::INITIALIZATION_VECTOR);
                $this->activeSession = json_decode($data, false);
            }
        } catch (InvalidArgumentException $e) {
            // ignore??
        }

    }

    public function clearSessionCache(): void
    {

        if (! isset($this->cache)) {
            return;
        }

        try {
            $this->cache->delete($this->getCacheKey());
        } catch (InvalidArgumentException $e) {
            // report??
        }
    }

    public function toCache(object $sessionData): void
    {
        if (! isset($this->cache)) {
            return;
        }

        $encryptedValue = openssl_encrypt(data: json_encode($sessionData), cipher_algo: self::ENCRYPTION_METHOD, passphrase: $this->salt, options: 0, iv: self::INITIALIZATION_VECTOR);
        try {
            $this->cache->set($this->getCacheKey(), $encryptedValue);
        } catch (InvalidArgumentException $e) {
            // ignore??
        }
    }
}
