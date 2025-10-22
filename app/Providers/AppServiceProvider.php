<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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
        // Ensure database always uses UTC
        config(['app.timezone' => 'UTC']);
        date_default_timezone_set('UTC');

        // Register timezone helper
        if (!class_exists('TimezoneHelper')) {
            class_alias(\App\Helpers\TimezoneHelper::class, 'TimezoneHelper');
        }
    }
}
