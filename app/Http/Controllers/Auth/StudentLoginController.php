<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentLoginController extends Controller
{
    /**
     * Show student login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('student.dashboard');
        }

        return view('auth.student-login');
    }

    /**
     * Handle student login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Please enter your registered student email address.',
            'email.email' => 'Please provide a valid email format (e.g., student@university.edu).',
            'password.required' => 'Please enter your account password.',
        ]);

        // Check if account exists and is deactivated
        $user = User::where('email', $credentials['email'])
            ->where('role', 'student')
            ->first();

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Your student account is currently deactivated. Please contact the examination cell.',
            ])->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => 'student',
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('student.dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password credentials provided.',
        ])->onlyInput('email');
    }

    /**
     * Handle student logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'You have been logged out safely.');
    }
}
