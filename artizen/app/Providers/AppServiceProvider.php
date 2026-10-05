<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share navigation categories (with subcategories) to ALL views globally.
        // Used by layouts.navbar for the dynamic header ribbon + mega-dropdowns.
        View::composer('*', function ($view) {
            static $navCategories = null;
            if ($navCategories === null) {
                try {
                    $navCategories = Category::where('active', true)
                        ->with(['activeSubcategories'])
                        ->orderBy('display_order')
                        ->orderBy('id')
                        ->get();
                } catch (\Throwable $e) {
                    $navCategories = collect();
                }
            }
            $view->with('navCategories', $navCategories);
        });
    }
}
