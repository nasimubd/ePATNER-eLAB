<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        // Check if user is already authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->roles->first()->name ?? null;

            // Direct URL redirects instead of named routes to avoid potential redirect loops
            $redirectUrl = match ($role) {
                'super-admin' => '/super-admin/dashboard',
                'admin' => '/admin/dashboard',
                'staff' => '/admin/inventory/inventory_transactions',
                default => '/login'
            };

            return redirect($redirectUrl);
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $role = $user->roles->first()->name ?? null;

        // Direct URL redirects instead of named routes to avoid potential redirect loops
        $redirectUrl = match ($role) {
            'super-admin' => '/super-admin/dashboard',
            'admin' => '/admin/dashboard',
            'Manager' => '/admin/dashboard',
            'LA' => '/admin/dashboard',
            default => '/login'
        };

        return redirect()->intended($redirectUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
