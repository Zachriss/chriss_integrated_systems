<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only redirect if the user is trying to access login or register routes
            if ($request->is('login') || $request->is('register') || $request->is('password/*')) {
                if ($user->isSuperAdmin()) {
                    return redirect()->route('super-admin.dashboard');
                }

                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->isStaff()) {
                    return redirect()->route('staff.dashboard');
                }

                return redirect()->route('dashboard.index');
            }
        }

        return $next($request);
    }
}
