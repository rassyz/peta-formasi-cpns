<?php

use App\Http\Controllers\BerandaController;
use App\Http\Controllers\FormasiController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Finder;
use App\Http\Controllers\AkunController;
use App\Livewire\Admin\KodeAksesManager;
use App\Livewire\RedeemForm;

Route::get('/', BerandaController::class)->name('home');

Route::view('/fondasi', 'fondasi')->name('fondasi');
Route::view('/toolkit', 'toolkit')->name('toolkit');
Route::get('/formasi', Finder::class)->name('formasi.index');
Route::get('/formasi/{slug}', [FormasiController::class, 'show'])->name('formasi.show');
Route::get('/jurusan/{slug}', [FormasiController::class, 'jurusan'])->name('jurusan.show');
Route::view('/harga', 'harga')->name('harga');


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', \App\Http\Controllers\Admin\BerandaController::class)->name('beranda');
        Route::get('/kode-akses', KodeAksesManager::class)->name('kode-akses');
        Route::livewire('/impor', App\Livewire\Admin\ImporFormasi::class)->name('impor');
        Route::get('/impor/template', App\Http\Controllers\Admin\TemplateImporController::class)->name('impor.template');
        Route::livewire('/formasi', App\Livewire\Admin\FormasiManager::class)->name('formasi');
        Route::get('/peserta', \App\Livewire\Admin\PesertaManager::class)->name('peserta');
        Route::get('/musim', \App\Livewire\Admin\MusimManager::class)->name('musim');
    });

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (\Illuminate\Http\Request $request) {
        return $request->user()?->isAdmin()
            ? redirect()->route('admin.beranda')
            : redirect('/');
    })->name('dashboard');
});

Route::middleware('auth')->prefix('akun')->name('akun.')->group(function () {
    Route::get('/pass', [AkunController::class, 'pass'])->name('pass');
    Route::get('/redeem', RedeemForm::class)->name('redeem');
});

require __DIR__ . '/settings.php';
