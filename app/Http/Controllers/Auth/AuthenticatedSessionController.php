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
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Autentikasi user bawaan Breeze
        $request->authenticate();

        // 2. Cek apakah user aktif
        $user = $request->user();
        if (!$user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan/diblokir oleh admin.',
            ]);
        }

        // 3. Regenerasi session untuk mencegah session fixation
        $request->session()->regenerate();

        // 3. Redirect berdasarkan role
        if ($request->user()->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        // Default redirect untuk user biasa
        return redirect()->intended('/user/dashboard');
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