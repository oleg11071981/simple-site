<?php

namespace App\Libraries;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\HTTP\RequestInterface;

class LoginThrottler
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900;

    private CacheInterface $cache;

    public function __construct(?CacheInterface $cache = null)
    {
        $this->cache = $cache ?? cache();
    }

    public function isLocked(RequestInterface $request): bool
    {
        return $this->getAttempts($request) >= self::MAX_ATTEMPTS;
    }

    public function getAttempts(RequestInterface $request): int
    {
        return (int) ($this->cache->get($this->cacheKey($request)) ?? 0);
    }

    public function recordFailure(RequestInterface $request): void
    {
        $attempts = $this->getAttempts($request) + 1;
        $this->cache->save($this->cacheKey($request), $attempts, self::LOCKOUT_SECONDS);
    }

    public function clear(RequestInterface $request): void
    {
        $this->cache->delete($this->cacheKey($request));
    }

    public function lockoutMessage(): string
    {
        return 'Слишком много неудачных попыток входа. Повторите через 15 минут.';
    }

    private function cacheKey(RequestInterface $request): string
    {
        return 'auth_login_attempts_' . md5($request->getIPAddress());
    }
}
