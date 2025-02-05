<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employees extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'total_project',
    ];

    protected $table = 'employees';

    public function penanggungjawab(): HasMany
    {
        return $this->hasMany(Projects::class);
    }

    public function anggota(): HasMany
    {
        return $this->hasMany(Detail_Project::class);
    }
}
