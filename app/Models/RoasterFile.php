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

    public function setProcessing()
    {
        $this->status = 'processing';
        $this->save();
    }

    public function setCompleted()
    {
        $this->status = 'completed';
        $this->save();
    }

    public function setFailed(string $output)
    {
        $this->status = 'failed';
        $this->error_message = $output;
        $this->save();
    }
}
