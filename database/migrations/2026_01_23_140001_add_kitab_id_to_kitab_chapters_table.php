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
        Schema::table('kitab_chapters', function (Blueprint $table) {
            $table->foreignId('kitab_id')->nullable()->after('id')->constrained('kitabs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitab_chapters', function (Blueprint $table) {
            $table->dropForeign(['kitab_id']);
            $table->dropColumn('kitab_id');
        });
    }
};
