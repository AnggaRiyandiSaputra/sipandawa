<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Detail_Project extends Model
{
    protected $fillable = [
        'projects_id',
        'anggota_id_anggota',
    ];

    protected $table = 'detail_project';

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'anggota_id_anggota');
    }
}
