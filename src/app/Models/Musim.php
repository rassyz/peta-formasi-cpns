<?php

namespace App\Models;

use App\Models\Formasi;
use App\Models\KodeAkses;
use App\Models\RequestFormasi;
use App\Models\UserPass;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Musim extends Model
{
    protected $table = 'musim';

    protected $fillable = [
        'tahun',
        'label',
        'tanggal_acuan_usia',
        'tanggal_pengumuman',
        'pendaftaran_mulai',
        'pendaftaran_tutup',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'tanggal_acuan_usia' => 'date',
            'tanggal_pengumuman' => 'date',
            'pendaftaran_mulai' => 'date',
            'pendaftaran_tutup' => 'date',
        ];
    }

    public function formasi(): HasMany
    {
        return $this->hasMany(Formasi::class);
    }

    public function kodeAkses(): HasMany
    {
        return $this->hasMany(KodeAkses::class);
    }

    public function userPass(): HasMany
    {
        return $this->hasMany(UserPass::class);
    }

    public function requestFormasi(): HasMany
    {
        return $this->hasMany(RequestFormasi::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeHistoris(Builder $query): Builder
    {
        return $query->where('status', 'historis');
    }

    /**
     * Musim default tampilan Finder (PRD §7.1): musim aktif bila ada,
     * selain itu musim historis terakhir (tahun terbesar).
     */
    public static function defaultTampil(): ?self
    {
        return static::aktif()->orderByDesc('tahun')->first()
            ?? static::historis()->orderByDesc('tahun')->first();
    }
}
