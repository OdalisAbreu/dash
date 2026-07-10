<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyStat extends Model
{
    protected $fillable = ['year', 'month', 'total_appointments', 'new_appointments'];
}
