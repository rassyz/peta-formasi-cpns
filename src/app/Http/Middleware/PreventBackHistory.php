<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cegah halaman ber-login tampil lagi dari cache browser setelah pengguna
 * keluar (tombol Back) — dipasang 15 Nov 2026.
 *
 * Tanpa header ini, browser boleh menyajikan ulang halaman admin/peserta
 * dari cache lokal walau sesinya sudah dihancurkan: isinya terlihat lagi
 * (walau aksi apa pun tetap ditolak server). Dengan no-store, browser
 * wajib meminta ulang ke server — dan server mengarahkannya ke login.
 *
 * Ruang lingkup SENGAJA hanya respons untuk pengguna yang sedang masuk;
 * halaman publik untuk tamu tidak disentuh agar tetap bisa dicache
 * seperti biasa.
 */
class PreventBackHistory
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (Auth::check()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
