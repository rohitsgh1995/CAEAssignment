<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoasterFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'mime',
        'path',
    ];
}
