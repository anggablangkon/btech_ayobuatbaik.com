<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('qurban_coupon_scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('qurban_participant_id');
            $table->string('coupon_code', 100);
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table
                ->foreign('qurban_participant_id')
                ->references('id')
                ->on('qurban_participants')
                ->cascadeOnDelete();

            $table->index(['created_at', 'qurban_participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qurban_coupon_scans');
    }
};
