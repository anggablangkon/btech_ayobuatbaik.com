<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'message',
        'image_path',
        'target',
        'target_data',
        'status',
        'processed_count',
        'total_count',
    ];

    protected $casts = [
        'target_data' => 'array',
    ];
}
