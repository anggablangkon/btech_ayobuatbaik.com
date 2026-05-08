<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\Interfaces\ColorInterface;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qurban_participant_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('qurban_participant_id');
            $table->enum('qurban_type', ['sapi', 'kambing', 'domba', 'unta'])->nullable();
            $table->integer('price')->unsigned()->nullable()->default(0);
            $table->integer('total_coupon')->unsigned()->nullable()->default(0);
            $table->integer('total_price')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('qurban_participant_id')->references('id')->on('qurban_participants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qurban_participant_items');
    }
};
