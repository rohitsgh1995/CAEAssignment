<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoasterFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'extension',
        'path',
        'status',
        'error_message',
    ];
}
