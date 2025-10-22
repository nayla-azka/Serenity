<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SetTimezone
{
    public function handle(Request $request, Closure $next)
    {
        // Get timezone from session or default to UTC
        $timezone = session('timezone', config('app.timezone', 'UTC'));
        
        // Validate timezone
        if (!in_array($timezone, timezone_identifiers_list())) {
            $timezone = 'UTC';
            session(['timezone' => 'UTC']);
        }
        
        // Store the original app timezone for database operations (should always be UTC)
        if (!config('app.original_timezone')) {
            config(['app.original_timezone' => config('app.timezone')]);
        }
        
        // Set session timezone for display purposes only
        session(['timezone' => $timezone]);
        
        return $next($request);
    }
}