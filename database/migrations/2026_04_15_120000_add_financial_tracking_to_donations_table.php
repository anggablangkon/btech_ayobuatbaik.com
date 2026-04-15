<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedBigInteger('gross_amount')->nullable()->after('amount');
            $table->unsignedBigInteger('midtrans_fee_amount')->nullable()->after('gross_amount');
            $table->unsignedBigInteger('net_amount')->nullable()->after('midtrans_fee_amount');
            $table->string('payment_type')->nullable()->after('donation_type');
            $table->timestamp('settlement_time')->nullable()->after('status_change_at');
            $table->json('midtrans_payload')->nullable()->after('snap_token');
            $table->string('net_amount_source')->nullable()->after('net_amount');

            $table->index(['payment_type', 'net_amount_source'], 'donations_payment_finance_idx');
        });

        DB::table('donations')
            ->whereNull('gross_amount')
            ->update([
                'gross_amount' => DB::raw('amount'),
            ]);

        DB::table('donations')
            ->where(function ($query) {
                $query->where('donation_code', 'like', 'MANUAL-%')
                    ->orWhereIn('donation_type', ['manual', 'cash']);
            })
            ->update([
                'payment_type' => 'manual_entry',
                'net_amount_source' => 'not_applicable_manual',
            ]);

        DB::table('donations')
            ->where('status', 'success')
            ->whereNull('net_amount_source')
            ->where(function ($query) {
                $query->where('donation_code', 'not like', 'MANUAL-%')
                    ->where(function ($nested) {
                        $nested->whereNull('donation_type')
                            ->orWhereNotIn('donation_type', ['manual', 'cash']);
                    });
            })
            ->update([
                'net_amount_source' => 'pending_reconciliation',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex('donations_payment_finance_idx');
            $table->dropColumn([
                'gross_amount',
                'midtrans_fee_amount',
                'net_amount',
                'payment_type',
                'settlement_time',
                'midtrans_payload',
                'net_amount_source',
            ]);
        });
    }
};
