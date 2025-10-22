<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        Visit::create([
            'user_id'    => Auth::check() ? Auth::id() : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $next($request);
    }
}

