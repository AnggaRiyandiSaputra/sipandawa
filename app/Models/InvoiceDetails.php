<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetails extends Model
{
    protected $fillable = [
        'invoices_id',
        'item',
        'qty',
        'price',
        'total'
    ];

    protected $table = 'invoice_details';
}
