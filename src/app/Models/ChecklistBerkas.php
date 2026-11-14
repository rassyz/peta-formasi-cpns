<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistBerkas extends Model
{
    protected $table = 'checklist_berkas';

    protected $fillable = [
        'instansi_id',
        'formasi_id',
        'nama_dokumen',
        'format',
        'ukuran_maks_kb',
        'wajib',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'ukuran_maks_kb' => 'integer',
            'wajib' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    public function instansi(): BelongsTo
    {
        return $this->belongsTo(Instansi::class);
    }

    public function formasi(): BelongsTo
    {
        return $this->belongsTo(Formasi::class);
    }
}
