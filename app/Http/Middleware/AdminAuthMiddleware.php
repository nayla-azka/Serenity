<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login.form'); // make sure route exists
        }

        // Optionally, check if role is admin or operator
        if (!in_array(Auth::user()->role, ['admin', 'konselor'])) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

