<?php

namespace App\Models;

use App\Models\Formasi;
use App\Models\SnapshotPelamar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormasiKuota extends Model
{
    protected $table = 'formasi_kuota';

    protected $fillable = [
        'formasi_id',
        'jenis_kebutuhan',
        'jumlah',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function formasi(): BelongsTo
    {
        return $this->belongsTo(Formasi::class);
    }

    public function snapshot(): HasMany
    {
        return $this->hasMany(SnapshotPelamar::class);
    }

    public function snapshotTerakhir(): HasOne
    {
        return $this->hasOne(SnapshotPelamar::class)->latestOfMany('tanggal_snapshot');
    }

    /**
     * Rasio peluang (FR-C2) = pelamar MS snapshot terakhir / kuota.
     * Null bila snapshot belum ada atau kuota 0.
     */
    public function rasio(): ?float
    {
        $snapshot = $this->snapshotTerakhir;

        if ($snapshot === null || $this->jumlah === 0) {
            return null;
        }

        return $snapshot->jumlah_ms / $this->jumlah;
    }

    /**
     * Persentase peluang (FR-C2) = kuota / pelamar MS snapshot terakhir x 100%.
     * Null bila snapshot belum ada atau pelamar MS 0 (formasi tanpa pelamar MS:
     * persentase tidak terhingga — kasusnya ditangani filter "0 pelamar", FR-A3).
     */
    public function peluangPersen(): ?float
    {
        $snapshot = $this->snapshotTerakhir;

        if ($snapshot === null || $snapshot->jumlah_ms === 0) {
            return null;
        }

        return $this->jumlah / $snapshot->jumlah_ms * 100;
    }
}
