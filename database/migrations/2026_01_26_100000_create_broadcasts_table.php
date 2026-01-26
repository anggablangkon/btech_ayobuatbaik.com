<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('message');
            $table->string('image_path')->nullable();
            $table->string('target'); // all, donors, karyawan, numbers
            $table->json('target_data')->nullable(); // For storing custom numbers etc
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->integer('processed_count')->default(0);
            $table->integer('total_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
