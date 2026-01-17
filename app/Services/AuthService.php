<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\AuthenticationException;
use App\Services\Auth\AuthProvider;

class AuthService implements AuthProvider
{
    public function attemptLogin(
        string $identifier,
        string $password,
        bool $remember = false,
        string $guard = 'web'
    ): array {
        $key = 'login_attempts_' . request()->ip();
        $maxAttempts = (int) config('auth-service.login.max_attempts', 5);
        $lockoutSeconds = (int) config('auth-service.login.lockout_seconds', 300);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            throw new AuthenticationException("Quá nhiều lần thử đăng nhập. Vui lòng thử lại sau $seconds giây.");
        }

        $credentials = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $identifier, 'password' => $password]
            : ['username' => $identifier, 'password' => $password];

        if (Auth::guard($guard)->attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                throw new AuthenticationException('Tài khoản đã bị khóa.');
            }

            request()->session()->regenerate();

            RateLimiter::clear($key);

            if ($user->two_factor_secret) {
                session(['2fa_user_id' => $user->id]);
                Auth::logout();
                return [
                    'status' => false,
                    'message' => 'Yêu cầu xác thực hai yếu tố.',
                    'redirect' => route('2fa.verify'),
                ];
            }

            return ['status' => true, 'message' => 'Đăng nhập thành công.'];
        }

        RateLimiter::hit($key, $lockoutSeconds);

        return ['status' => false, 'message' => 'Tài khoản hoặc mật khẩu không chính xác.'];
    }

    public function attempt(array $credentials, bool $remember = false, string $guard = 'web'): array
    {
        $identifier = $credentials['identifier'] ?? '';
        $password = $credentials['password'] ?? '';
        return $this->attemptLogin($identifier, $password, $remember, $guard);
    }
}