<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek 1 : Apakah user sudah login?
        // Cek 2 : Apaka user tersebut adalah Admin?
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
            // Silahkan Masuk
        }

        // Jika Bukan
        // Kembalikan ke halaman login
        return redirect()->route('login')->with('error', 'Anda tidak memiliki akses');
    }
}
