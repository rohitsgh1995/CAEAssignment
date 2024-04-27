<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'roaster_id',
        'activity',
        'code',
        'description',
        'remark',
        'from',
        'std',
        'to',
        'sta',
        'remarks',
        'blh',
        'flight_time',
        'night_time',
        'duration',
        'ext',
        'pax_booked',
        'ac_reg',
    ];
}
