<?php

namespace App\Models;

use App\Models\ChecklistBerkas;
use App\Models\Formasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instansi extends Model
{
    protected $table = 'instansi';

    protected $fillable = [
        'nama',
        'slug',
        'wilayah',
        'situs_resmi',
        'link_pengumuman',
    ];

    public function formasi(): HasMany
    {
        return $this->hasMany(Formasi::class);
    }

    public function checklistBerkas(): HasMany
    {
        return $this->hasMany(ChecklistBerkas::class);
    }

    /** Baris checklist default instansi ini (yang tidak menempel ke formasi tertentu). */
    public function checklistDefault(): HasMany
    {
        return $this->hasMany(ChecklistBerkas::class)->whereNull('formasi_id');
    }
}
