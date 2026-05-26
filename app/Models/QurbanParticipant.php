<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class QurbanParticipant extends Model
{
    /** @use HasFactory<\Database\Factories\QurbanParticipantFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "full_name",
        "nik",
        "contact_number",
        "email",
        "address",
        "city",
        "province",
        "postal_code",
        "country",
        "coupon_code",
        "status",
        "note",
        "total_coupon",
        "image_qr_path",
        "pickup_date",
        "pickup_time",
    ];

    public static function generateCouponCode(): string
    {
        do {
            $couponCode = Str::upper(Str::random(8));
        } while (self::withTrashed()->where("coupon_code", $couponCode)->exists());

        return $couponCode;
    }

    /**
     * Get all of the items for the QurbanParticipant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(QurbanParticipantItem::class, "qurban_participant_id");
    }

    public function couponScans(): HasMany
    {
        return $this->hasMany(QurbanCouponScan::class, "qurban_participant_id");
    }

    public function scopeTableSearch($query)
    {
        $status = request()->get("status", null);
        $startDate = request()->get("start_date", Carbon::now()->startOfMonth()->format("Y-m-d"));
        $endDate = request()->get("end_date", Carbon::now()->endOfMonth()->format("Y-m-d"));
        $search = request()->get("search", null);
        if ($status) {
            $query->where("status", $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("full_name", "like", "%{$search}%")
                    ->orWhere("nik", "like", "%{$search}%")
                    ->orWhere("contact_number", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%")
                    ->orWhere("address", "like", "%{$search}%")
                    ->orWhere("city", "like", "%{$search}%")
                    ->orWhere("province", "like", "%{$search}%")
                    ->orWhere("postal_code", "like", "%{$search}%")
                    ->orWhere("country", "like", "%{$search}%");
            });
        }

        $query->whereBetween("created_at", [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
    }

    public static function getStatusClass(string $status): string
    {
        return match ($status) {
            "pending" => "bg-yellow-100 text-yellow-800",
            "taken" => "bg-green-100 text-green-800",
            "rejected" => "bg-red-100 text-red-800",
            "sended" => "bg-blue-100 text-blue-800",
            default => "bg-gray-100 text-gray-800",
        };
    }
}
