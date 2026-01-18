<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Custom validation rule for Cloudflare Turnstile with local bypass and logging.
 */
class Turnstile implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Log giá trị để debug (chỉ ở local/dev)
        if (app()->environment('local', 'testing')) {
            Log::debug('Turnstile value received: ' . $value);
        }

        // Bypass hoàn toàn ở local/testing hoặc với dummy keys (1x/2x/3x cho testing)
        if (
            app()->environment('local', 'testing') ||
            str_starts_with(env('TURNSTILE_SECRET_KEY'), '1x') ||
            str_starts_with(env('TURNSTILE_SECRET_KEY'), '2x') ||
            str_starts_with(env('TURNSTILE_SECRET_KEY'), '3x')
        ) {
            Log::info('Turnstile bypassed for local/testing/dummy keys.');
            return true; // Always pass ở mode demo
        }

        // Verify thực với Cloudflare API
        $response = Http::asForm()
            ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => env('TURNSTILE_SECRET_KEY'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

        $success = $response->successful() && $response->json('success') === true;

        // Log kết quả verify nếu fail
        if (!$success) {
            Log::warning('Turnstile verification failed: ' . json_encode($response->json()));
        }

        return $success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Xác thực CAPTCHA thất bại. Vui lòng thử lại.';
    }
}