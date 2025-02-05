<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Messages extends Model
{
    protected $fillable = [
        'employee_id',
        'client_id',
        'no_invoice',
        'name',
        'message',
        'schedule',
    ];

    protected $table = 'messages';

    public function Employee(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function Client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }
}
