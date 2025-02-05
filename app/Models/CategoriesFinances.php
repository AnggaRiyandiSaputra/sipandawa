<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoriesFinances extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    protected $table = 'categories_finances';
}
