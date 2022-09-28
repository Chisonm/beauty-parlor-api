<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
         //    check if user role is Admin
        if (auth()->user()->role == 'admin') {
            return $next($request);
        }

        return response()->json(['message' => 'You are not authorized to access this page.', 'status_code' => 401, 'success' => false], 401);
    }
}
