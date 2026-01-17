<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Fahlisaputra\Minify\Middleware\MinifyHtml;
use Fahlisaputra\Minify\Middleware\MinifyCss;
use Fahlisaputra\Minify\Middleware\MinifyJavascript;
use Symfony\Component\HttpFoundation\Response;

class ConditionalMinify
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip nếu path bắt đầu bằng /admin (backend)
        if (str_starts_with($request->path(), 'admin')) {
            return $next($request);  // Không minify
        }

        // Áp dụng minify cho frontend
        $response = $next($request);

        // Minify CSS trước
        if (config('minify.css.enabled')) {
            $response = (new MinifyCss())->handle($request, fn() => $response);
        }

        // Minify JS trước
        if (config('minify.js.enabled')) {
            $response = (new MinifyJavascript())->handle($request, fn() => $response);
        }

        // Minify HTML cuối
        if (config('minify.html.enabled')) {
            $response = (new MinifyHtml())->handle($request, fn() => $response);
        }

        return $response;
    }
}