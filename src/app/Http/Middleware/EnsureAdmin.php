<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Hanya akun ber-role 'admin' yang boleh lewat (route /admin/*, PRD §3).
     * Dipakai setelah middleware 'auth': Route::middleware(['auth', 'admin']).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            abort(403, 'Khusus admin.');
        }

        return $next($request);
    }
}
