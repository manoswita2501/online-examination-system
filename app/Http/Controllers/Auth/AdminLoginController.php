<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Show administrator login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('student.dashboard');
        }

        return view('auth.admin-login');
    }

    /**
     * Handle administrator login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'admin_id' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'password' => ['required', 'string'],
        ], [
            'admin_id.required' => 'Please enter your 5-digit Admin ID.',
            'admin_id.regex' => 'The Admin ID must be exactly 5 numeric digits (e.g., 01234).',
            'password.required' => 'Please enter your administrator password.',
        ]);

        $admin = User::where('admin_id', $credentials['admin_id'])
            ->where('role', 'admin')
            ->first();

        if ($admin && !$admin->is_active) {
            return back()->withErrors([
                'admin_id' => 'This administrator account is currently inactive.',
            ])->onlyInput('admin_id');
        }

        if (Auth::attempt([
            'admin_id' => $credentials['admin_id'],
            'password' => $credentials['password'],
            'role' => 'admin',
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome to the Administrator Control Center, ' . Auth::user()->name . '.');
        }

        return back()->withErrors([
            'admin_id' => 'Invalid administrator credentials provided.',
        ])->onlyInput('admin_id');
    }

    /**
     * Handle administrator logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Administrator session ended successfully.');
    }
}
