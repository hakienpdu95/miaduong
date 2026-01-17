<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
            'turnstileResponse' => 'required|string', // Validation cho CAPTCHA nếu cần
            'remember' => 'boolean',
        ]);

        // Logic tùy chỉnh: Xác định field là email hay username
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt login
        $credentials = [
            $field => $request->identifier,
            'password' => $request->password,
            'is_active' => 1, // Chỉ login nếu user active (dựa trên bảng users)
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard'); // Redirect đến dashboard backend
        }

        // Login thất bại
        throw ValidationException::withMessages([
            'identifier' => __('auth.failed'), // Hoặc message tùy chỉnh: 'Thông tin đăng nhập không đúng'
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}