<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KodeAkses extends Model
{
    protected $table = 'kode_akses';

    protected $fillable = [
        'kode',
        'tipe_pass',
        'musim_id',
        'status',
        'user_id',
        'dipakai_pada',
        'batch',
    ];

    protected function casts(): array
    {
        return [
            'dipakai_pada' => 'datetime',
        ];
    }

    public function musim(): BelongsTo
    {
        return $this->belongsTo(Musim::class);
    }

    /** Pengguna yang memakai kode ini (null selama belum dipakai). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userPass(): HasOne
    {
        return $this->hasOne(UserPass::class);
    }

    public function belumDipakai(): bool
    {
        return $this->status === 'belum_dipakai';
    }
}
