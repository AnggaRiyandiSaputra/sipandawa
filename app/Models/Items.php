<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{

    protected $fillable = [
        'no_item',
        'name',
        'description',
        'image',
        'qty'
    ];

    protected $table = 'items';
}
