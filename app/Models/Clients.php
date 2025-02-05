<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clients extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'phone',
        'priority',
        'email',
    ];

    protected $table = 'clients';

    public function projects(): HasMany
    {
        return $this->hasMany(Projects::class);
    }
}
