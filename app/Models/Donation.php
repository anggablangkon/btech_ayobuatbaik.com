<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        "payment_type",
        "amount",
        "gross_amount",
        "midtrans_fee_amount",
        "net_amount",
        "net_amount_source",
        "note",
        "user_id",
        "status",
        "status_change_at",
        "settlement_time",
        "reminder_sent_at",
        "followup_sent_at",
        "snap_token",
        "midtrans_payload",
        "expires_at",
        // UTM Tracking
        "utm_source",
        "utm_medium",
        "utm_campaign",
    ];

    protected $casts = [
        "amount" => "integer",
        "gross_amount" => "integer",
        "midtrans_fee_amount" => "integer",
        "net_amount" => "integer",
        "status_change_at" => "datetime",
        "settlement_time" => "datetime",
        "reminder_sent_at" => "datetime",
        "followup_sent_at" => "datetime",
        "midtrans_payload" => "array",
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

    public function isManualDonation(): bool
    {
        return Str::startsWith((string) $this->donation_code, "MANUAL-")
            || in_array(strtolower((string) $this->donation_type), ["manual", "cash"], true);
    }

    public function isMidtransDonation(): bool
    {
        return !$this->isManualDonation();
    }

    public function getGrossAmountValueAttribute(): int
    {
        return (int) ($this->gross_amount ?? $this->amount ?? 0);
    }

    public function getFeeAmountValueAttribute(): ?int
    {
        return $this->midtrans_fee_amount;
    }

    public function getNetAmountValueAttribute(): ?int
    {
        return $this->net_amount;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        if ($this->isManualDonation()) {
            return "Manual / Non-Midtrans";
        }

        if (blank($this->payment_type)) {
            return "Midtrans";
        }

        return Str::of($this->payment_type)
            ->replace("_", " ")
            ->title()
            ->toString();
    }

    public function getFinancialStatusLabelAttribute(): string
    {
        if ($this->isManualDonation()) {
            return "Tidak kena fee Midtrans";
        }

        return match ($this->net_amount_source) {
            "midtrans_report" => "Final dari report Midtrans",
            "midtrans_status_api" => "Final dari Midtrans API",
            "estimated_payment_rule" => "Estimasi otomatis",
            "pending_reconciliation" => "Menunggu sinkronisasi fee",
            default => "Belum dihitung",
        };
    }
}
