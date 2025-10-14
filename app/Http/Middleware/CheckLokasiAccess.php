<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLokasiAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admin bisa akses semua
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Petugas harus punya lokasi_id
        if ($user && $user->isPetugas()) {
            if (!$user->lokasi_id) {
                abort(403, 'Anda belum ditugaskan ke lokasi manapun. Silakan hubungi administrator.');
            }
        }

        return $next($request);
    }
}