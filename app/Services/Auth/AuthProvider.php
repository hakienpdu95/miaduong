<?php

namespace App\Services\Auth;

interface AuthProvider
{
    /**
     * Attempt authentication with credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @param string $guard
     * @return array
     */
    public function attempt(array $credentials, bool $remember = false, string $guard = 'web'): array;
}