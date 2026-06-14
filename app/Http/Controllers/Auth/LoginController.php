<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && $user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'This account is inactive. Contact the system administrator.',
            ]);
        }

        $user?->forceFill([
            'last_login_at' => now(),
        ])->save();

        return redirect()->intended($this->redirectPathFor($user))
            ->with('success', 'Welcome back, '.$request->user()->full_name.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'You have been logged out successfully.');
    }

    private function redirectPathFor($user): string
    {
        if ($user && $user->isSuperAdmin()) {
            return route('super-admin.dashboard');
        }

        if ($user && $user->isAdmin()) {
            return route('admin.dashboard');
        }

        if ($user && $user->isStaff()) {
            return route('staff.dashboard');
        }

        // Customer dashboard
        return route('customer.dashboard');
    }
}
