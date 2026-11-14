<?php

namespace App\Models;

use App\Models\KodeAkses;
use App\Models\Musim;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPass extends Model
{
    protected $table = 'user_pass';

    protected $fillable = [
        'user_id',
        'kode_akses_id',
        'tipe_pass',
        'musim_id',
        'aktif_dari',
        'aktif_hingga',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'aktif_dari' => 'datetime',
            'aktif_hingga' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kodeAkses(): BelongsTo
    {
        return $this->belongsTo(KodeAkses::class);
    }

    public function musim(): BelongsTo
    {
        return $this->belongsTo(Musim::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif')
            ->where(function (Builder $q) {
                $q->whereNull('aktif_hingga')
                    ->orWhere('aktif_hingga', '>', now());
            });
    }

    public function isAktif(): bool
    {
        return $this->status === 'aktif'
            && ($this->aktif_hingga === null || now()->lessThan($this->aktif_hingga));
    }
}
