<?php

namespace App\Models;

use App\Models\Formasi;
use App\Models\FormasiKualifikasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    protected $table = 'jurusan';

    protected $fillable = [
        'jenjang',
        'nama',
        'slug',
    ];

    public function kualifikasi(): HasMany
    {
        return $this->hasMany(FormasiKualifikasi::class);
    }

    public function formasi(): BelongsToMany
    {
        return $this->belongsToMany(Formasi::class, 'formasi_kualifikasi')
            ->withTimestamps();
    }

    /** Label untuk pemilih jurusan, mis. "S1 — Teknik Informatika". */
    public function labelLengkap(): string
    {
        return "{$this->jenjang} — {$this->nama}";
    }
}
