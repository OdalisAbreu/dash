<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunity extends Model
{
    protected $fillable = [
        'client_id',
        'service',
        'amount',
        'category',
        'status',
        'month',
        'year',
        'pending_invoice',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'pending_invoice' => 'boolean',
    ];

    public const CATEGORIES = [
        'facturado' => 'Ganado / Facturado',
        'perdido' => 'Perdido',
        'pipeline' => 'Pipeline nuevo',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
