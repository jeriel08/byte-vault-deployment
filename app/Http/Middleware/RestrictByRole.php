<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RestrictByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = strtolower(Auth::user()->role); // Case-insensitive
            $path = $request->path(); // e.g., "dashboard", "products"

            // Skip restriction for logout
            if ($path === 'logout') {
                return $next($request);
            }

            // If Employee tries to access non-POS routes
            if ($role === 'employee' && !str_starts_with($path, 'pos')) {
                return redirect()->route('pos.products'); // Back to /pos
            }

            // Restrict Manager from specific admin routes
            if ($role === 'manager' && in_array($path, ['admin/account-manager', 'admin/audit'])) {
                return redirect()->route('dashboard'); // Assumes this route exists
            }
        }

        return $next($request);
    }
}
