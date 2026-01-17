<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MentorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login atau role-nya bukan mentor/admin, lempar ke home atau dashboard student
        if (!Auth::check() || !in_array(Auth::user()->role, ['mentor', 'admin'])) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses mentor.');
        }

        return $next($request);
    }
}
