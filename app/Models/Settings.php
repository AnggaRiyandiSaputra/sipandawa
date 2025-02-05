<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'kas',
        'pajak',
        'komisi'
    ];

    protected $table = 'settings';

    public function getSettings()
    {
        return $this->select('kas', 'pajak', 'komisi')->first();
    }
}
