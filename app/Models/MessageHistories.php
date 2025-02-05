<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageHistories extends Model
{
    protected $fillable = [
        'name_pj',
        'name_client',
        'send_to',
        'message',
        'status'
    ];

    protected $table = 'message_histories';
}
