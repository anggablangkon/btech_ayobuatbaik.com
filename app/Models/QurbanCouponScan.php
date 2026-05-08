<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QurbanCouponScan extends Model
{
    protected $fillable = [
        'qurban_participant_id',
        'coupon_code',
        'scanned_by',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(QurbanParticipant::class, 'qurban_participant_id');
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
