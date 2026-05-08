<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("qurban_participants", function (Blueprint $table) {
            $table->string("total_coupon")->nullable()->default(0);
            $table->string("image_qr_path", 255)->nullable();
            $table->string("pickup_date")->nullable();
            $table->string("pickup_time")->nullable();
            $table->dateTime("taken_date")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("qurban_participants", function (Blueprint $table) {
            $table->dropColumn("total_coupon");
            $table->dropColumn("image_qr_path");
            $table->dropColumn("pickup_date");
            $table->dropColumn("pickup_time");
            $table->dropColumn("taken_date");
        });
    }
};
