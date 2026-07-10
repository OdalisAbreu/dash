<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ['year', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
