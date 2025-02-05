<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Invoices extends Model
{
    protected $fillable = [
        'no_invoice',
        'employee_id',
        'client_id',
        'name',
        'description',
        'issued_date',
        'due_date',
        'paid_date',
        'is_paid',
        'image',
        'sub_total',
        'is_pajak',
        'diskon',
        'grand_total'
    ];

    protected $table = 'invoices';

    protected $casts = [
        'due_date' => 'datetime',
        'paid_date' => 'datetime'
    ];

    public function Employee(): BelongsTo
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function Client(): BelongsTo
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

    public function detailInvoice(): HasMany
    {
        return $this->hasMany(InvoiceDetails::class);
    }
}
