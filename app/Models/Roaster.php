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
        'check_out'
    ];

    public function activities($code = null)
    {
        if ($code) {
            return $this->hasMany(Activity::class)->where('code', $code);
        }

        return $this->hasMany(Activity::class);
    }

    public function flights($location = null)
    {
        if ($location) {
            return $this->hasMany(Activity::class)->where('code', 'FLT')->where('from', $location);
        }

        return $this->hasMany(Activity::class);
    }
}
