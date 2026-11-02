<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Check if user is authenticated
        if (!Auth::check()) {
            if ($role === 'admin') {
                return redirect()->route('admin.login')->with('error', 'Please log in with your Administrator credentials.');
            }
            return redirect()->route('student.login')->with('error', 'Please log in to access the Student Portal.');
        }

        $user = Auth::user();

        // 2. Check if account is active
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $targetRoute = $role === 'admin' ? 'admin.login' : 'student.login';
            return redirect()->route($targetRoute)->with('error', 'Your account has been deactivated. Please contact the administration.');
        }

        // 3. Check role authorization
        if ($user->role !== $role) {
            // If student attempts to access admin route
            if ($user->role === 'student' && $role === 'admin') {
                return redirect()->route('student.dashboard')->with('error', 'Access denied. You do not have administrator permissions.');
            }

            // If admin attempts to enter student exam flow
            if ($user->role === 'admin' && $role === 'student') {
                return redirect()->route('admin.dashboard')->with('info', 'Administrators cannot take student examinations. You have been redirected to the Admin Dashboard.');
            }

            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
