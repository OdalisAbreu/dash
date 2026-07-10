<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyGoal extends Model
{
    protected $fillable = ['year', 'month', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
