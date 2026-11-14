<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AkunController extends Controller
{
    /** Halaman "Pass Saya": daftar pass milik pengguna + status & masa berlaku. */
    public function pass(): View
    {
        /** @var User $pengguna */
        $pengguna = Auth::user();

        $passes = $pengguna->passes()
            ->with(['kodeAkses.musim'])
            ->latest('aktif_dari')
            ->get();

        return view('akun.pass', ['passes' => $passes]);
    }
}
