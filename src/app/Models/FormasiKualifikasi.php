<?php

namespace App\Models;

use App\Models\Formasi;
use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormasiKualifikasi extends Model
{
    protected $table = 'formasi_kualifikasi';

    protected $fillable = [
        'formasi_id',
        'jurusan_id',
    ];

    public function formasi(): BelongsTo
    {
        return $this->belongsTo(Formasi::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}
