<?php

namespace App\Models;

use App\Models\FormasiKuota;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SnapshotPelamar extends Model
{
    protected $table = 'snapshot_pelamar';

    protected $fillable = [
        'formasi_kuota_id',
        'tipe',
        'jumlah_ms',
        'jumlah_total',
        'tanggal_snapshot',
        'sumber',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_ms' => 'integer',
            'jumlah_total' => 'integer',
            'tanggal_snapshot' => 'date',
        ];
    }

    public function formasiKuota(): BelongsTo
    {
        return $this->belongsTo(FormasiKuota::class);
    }
}
