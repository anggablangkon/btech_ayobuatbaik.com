<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;

    // ✅ ONLY user-input fields & System fields
    protected $fillable = [
        "donation_code",
        "program_donasi_id",
        "donor_name",
        "donor_phone",
        "donor_email",
        "donation_type",
        "amount",
        "note",
        "user_id",
        "status",
        "status_change_at",
        "reminder_sent_at",
        "followup_sent_at",
        "snap_token",
        "expires_at",
        // UTM Tracking
        "utm_source",
        "utm_medium",
        "utm_campaign",
    ];

    protected $casts = [
        "amount" => "integer",
        "status_change_at" => "datetime",
        "reminder_sent_at" => "datetime",
        "followup_sent_at" => "datetime",
        "expires_at" => "datetime",
        "created_at" => "datetime",
        "updated_at" => "datetime",
        "deleted_at" => "datetime",
    ];

    public function program()
    {
        return $this->belongsTo(ProgramDonasi::class, "program_donasi_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setStatus(string $status): void
    {
        $this->attributes["status"] = $status;
        $this->attributes["status_change_at"] = now();
        $this->save();
    }
    public function setStatusUnpaid()
    {
        $this->setStatus("unpaid");
    }
    public function setStatusPending()
    {
        $this->setStatus("pending");
    }
    public function setStatusSuccess()
    {
        $this->setStatus("success");
    }
    public function setStatusFailed()
    {
        $this->setStatus("failed");
    }
    public function setStatusExpired()
    {
        $this->setStatus("expired");
    }

    // helper
    public function getDonorInitialAttribute()
    {
        // Ambil karakter pertama dari nama donatur
        if (!empty($this->donor_name)) {
            // Ambil huruf pertama dan ubah ke huruf kapital
            return strtoupper(substr($this->donor_name, 0, 1));
        }
        return "D"; // Default inisial jika nama kosong
    }
}
