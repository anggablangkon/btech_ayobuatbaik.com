<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan index untuk kolom yang sering digunakan dalam query filter
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Index untuk filter status (sangat sering dipakai)
            $table->index('status');
            
            // Index untuk filter by phone (untuk broadcast, followup)
            $table->index('donor_phone');
            
            // Composite index untuk query yang sering dipakai bersamaan
            $table->index(['status', 'created_at']);
            $table->index(['program_donasi_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['donor_phone']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['program_donasi_id', 'status']);
        });
    }
};
