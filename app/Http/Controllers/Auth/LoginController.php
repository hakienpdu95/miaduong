<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // Log toàn bộ request data để debug (chỉ ở local)
        if (app()->environment('local')) {
            Log::debug('Login request data: ' . json_encode($request->all()));
        }

        // Validation rules với bypass required cho turnstile ở local
        $rules = [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];

        // Chỉ required turnstile nếu không phải local/dummy
        if (!app()->environment('local', 'testing') && !str_starts_with(env('TURNSTILE_SITE_KEY'), '1x')) {
            $rules['turnstileResponse'] = ['required', 'string', new Turnstile];
        } else {
            $rules['turnstileResponse'] = ['nullable', 'string', new Turnstile]; // Optional ở local
        }

        $request->validate($rules);

        // Xác định field login: email hoặc username
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Credentials với điều kiện is_active
        $credentials = [
            $field => $request->identifier,
            'password' => $request->password,
            'is_active' => 1,
        ];

        // Attempt authentication
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            Log::info('Login successful for user: ' . $request->identifier);

            // Redirect đến dashboard backend
            return redirect()->intended(route('dashboard'));
        }

        // Nếu login thất bại, log và throw exception
        Log::warning('Login failed for identifier: ' . $request->identifier);
        throw ValidationException::withMessages([
            'identifier' => __('Thông tin đăng nhập không chính xác.'),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}