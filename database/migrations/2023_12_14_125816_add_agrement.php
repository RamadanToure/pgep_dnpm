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
        Schema::table('demande_etape', function (Blueprint $table) {
            $table->boolean('is_agrement')->default(false);
        });

        Schema::table('etape_type_demande', function (Blueprint $table) {
            $table->boolean('is_agrement')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_etape', function (Blueprint $table) {
            $table->dropColumn('is_agrement');
        });

        Schema::table('etape_type_demande', function (Blueprint $table) {
            $table->dropColumn('is_agrement');
        });
    }
};
