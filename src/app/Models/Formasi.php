<?php

namespace App\Models;

use App\Models\ChecklistBerkas;
use App\Models\FormasiKualifikasi;
use App\Models\FormasiKuota;
use App\Models\Instansi;
use App\Models\Jurusan;
use App\Models\Musim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formasi extends Model
{
    protected $table = 'formasi';

    protected $fillable = [
        'musim_id',
        'instansi_id',
        'jabatan',
        'penempatan',
        'slug',
        'usia_min',
        'usia_maks',
        'ipk_min',
        'skl_diterima',
        'syarat_akreditasi',
        'syarat_khusus',
        'gaji_min',
        'gaji_maks',
        'sumber_gaji',
        'tanggal_verifikasi_gaji',
        'sumber_pengumuman',
        'tanggal_verifikasi',
        'status_terbit',
        'kartu_contoh',
    ];

    protected function casts(): array
    {
        return [
            'usia_min' => 'integer',
            'usia_maks' => 'integer',
            'ipk_min' => 'decimal:2',
            'skl_diterima' => 'boolean',
            'gaji_min' => 'integer',
            'gaji_maks' => 'integer',
            'tanggal_verifikasi_gaji' => 'date',
            'tanggal_verifikasi' => 'date',
            'kartu_contoh' => 'boolean',
        ];
    }

    public function musim(): BelongsTo
    {
        return $this->belongsTo(Musim::class);
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function kualifikasi(): HasMany
    {
        return $this->hasMany(FormasiKualifikasi::class);
    }

    public function jurusan(): BelongsToMany
    {
        return $this->belongsToMany(Jurusan::class, 'formasi_kualifikasi')
            ->withTimestamps();
    }

    public function kuota(): HasMany
    {
        return $this->hasMany(FormasiKuota::class);
    }

    /** Baris checklist override milik formasi ini (bila ada). */
    public function checklistOverride(): HasMany
    {
        return $this->hasMany(ChecklistBerkas::class);
    }

    public function scopeTerbit(Builder $query): Builder
    {
        return $query->where('status_terbit', 'terbit');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status_terbit', 'draft');
    }

    /** Total kuota semua jenis kebutuhan (dipakai kartu polos & checksum FR-AD5). */
    public function totalKuota(): int
    {
        return (int) $this->kuota()->sum('jumlah');
    }
}
