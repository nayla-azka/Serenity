<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PublicAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('id_student')) {
            return redirect()->route('public.login.form');
        }

        return $next($request);
    }
}
