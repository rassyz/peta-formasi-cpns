<?php

namespace App\Models;

use App\Models\Musim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestFormasi extends Model
{
    protected $table = 'request_formasi';

    protected $fillable = [
        'musim_id',
        'user_id',
        'instansi_dicari',
        'jabatan_dicari',
        'catatan',
        'status',
    ];

    public function musim(): BelongsTo
    {
        return $this->belongsTo(Musim::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
