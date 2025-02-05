<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transactions extends Model
{
    protected $fillable = [
        'categorie_finance_id',
        'name',
        'description',
        'date_transaction',
        'total',
        'image',
        'no_projek',
    ];

    protected $table = 'transactions';

    public function CategoriesFinances(): BelongsTo
    {
        return $this->belongsTo(CategoriesFinances::class, 'categorie_finance_id');
    }
}
