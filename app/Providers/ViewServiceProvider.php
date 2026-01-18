<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Constants\ModuleConst;
use App\Services\BreadcrumbService;

/**
 * Service provider for composing views with dynamic data.
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compose sidebar (từ trước)
        View::composer('backend.partials.sidebar', function ($view) {
            ModuleConst::loadConfigs();
            $categories = ModuleConst::getAuthorizedModules(Auth::user());
            $view->with('categories', $categories);
        });

        // Compose breadcrumbs tự động cho tất cả views backend (e.g., backend.*)
        View::composer('backend.*', function ($view) {
            $breadcrumbService = app(BreadcrumbService::class);
            $breadcrumbs = $breadcrumbService->generate('backend');
            $view->with('breadcrumbs', $breadcrumbs);
        });
    }
}