<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QurbanParticipantItem extends Model
{
    /** @use HasFactory<\Database\Factories\QurbanParticipantItemFactory> */
    use HasFactory;
    use SoftDeletes;

    public const QURBAN_TYPES = ['sapi', 'kambing', 'domba', 'unta'];

    protected $fillable = [
        'qurban_participant_id',
        'qurban_type',
        'price',
        'total_coupon',
        'total_price',
    ];

    public function participant()
    {
        return $this->belongsTo(QurbanParticipant::class, 'qurban_participant_id');
    }
}
