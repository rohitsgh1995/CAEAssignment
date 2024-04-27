<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'check_in',
        'check_out',
        'blh',
        'flight_time',
        'night_time',
        'duration',
    ];
}
