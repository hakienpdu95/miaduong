<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str; // Để xử lý string

class CheckPermission
{
    // Map route method/action name → permission action (có thể config ở file riêng nếu cần)
    protected array $actionMap = [
        'index' => 'view',
        'show' => 'view',
        'create' => 'create',
        'store' => 'create',
        'edit' => 'edit',
        'update' => 'edit',
        'destroy' => 'delete',
    ];

    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Tự detect action từ route action name (ví dụ: 'index', 'destroy') hoặc method (GET/POST/...)
        $routeAction = $request->route()?->getActionMethod() ?? Str::afterLast($request->route()?->getName(), '.'); // Lấy từ route name (e.g., 'user-management.index' → 'index')
        $permissionAction = $this->actionMap[$routeAction] ?? 'view'; // Default 'view' nếu không map

        // Nếu cần tinh chỉnh dựa trên HTTP method (ví dụ: POST luôn 'create' trừ khi destroy)
        if ($request->method() === 'DELETE') {
            $permissionAction = 'delete';
        } elseif ($request->method() === 'POST' && $routeAction !== 'update') {
            $permissionAction = 'create';
        } elseif ($request->method() === 'PATCH' || $request->method() === 'PUT') {
            $permissionAction = 'edit';
        }

        if (!$user->hasPermission($module, $permissionAction)) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        return $next($request);
    }
}