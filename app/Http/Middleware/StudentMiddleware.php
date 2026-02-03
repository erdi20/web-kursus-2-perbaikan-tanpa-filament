<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login
        if (auth()->check()) {
            $role = auth()->user()->role;

            // Jika Mentor mencoba masuk, tendang ke Dashboard Mentor
            if ($role === 'mentor') {
                return redirect()->route('mentor.dashboardmentor');
            }

            // Jika Admin mencoba masuk, tendang ke Dashboard Admin
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        // Jika Siswa, silakan lanjut
        return $next($request);
    }
}
