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
        Schema::create("qurban_participants", function (Blueprint $table) {
            $table->increments("id");
            $table->string("full_name", 100)->nullable();
            $table->string("nik", 100)->nullable();
            $table->string("contact_number", 100)->nullable();
            $table->string("email", 100)->nullable();
            $table->string("address", 255)->nullable();
            $table->string("city", 100)->nullable();
            $table->string("province", 100)->nullable();
            $table->string("postal_code", 100)->nullable();
            $table->string("country", 100)->nullable();
            $table->string("coupon_code", 100)->nullable();
            $table
                ->enum("status", ["pending", "taken", "rejected", "sended"])
                ->nullable()
                ->default("pending");
            $table->text("note")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("qurban_participants");
    }
};
