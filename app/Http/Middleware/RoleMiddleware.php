<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Belum login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Akun nonaktif
        if (!$user->is_active) {
            Auth::logout();
            return redirect('/login')->withErrors(['email' => 'Akun kamu tidak aktif!']);
        }

        // 3. Role tidak sesuai → redirect ke dashboard masing-masing
        if ($user->role !== $role) {
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/user/dashboard');
        }

        return $next($request);
    }
}