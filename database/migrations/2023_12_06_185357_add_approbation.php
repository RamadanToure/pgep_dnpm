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
        Schema::table('demande', function (Blueprint $table) {
            $table->boolean('is_approuved')->default(false);
        });

        Schema::table('demande_etape', function (Blueprint $table) {
            $table->boolean('is_traitement')->default(false);
        });

        Schema::table('etape_type_demande', function (Blueprint $table) {
            $table->boolean('is_traitement')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande', function (Blueprint $table) {
            $table->dropColumn('is_approuved');
        });

        Schema::table('demande_etape', function (Blueprint $table) {
            $table->dropColumn('is_traitement');
        });

        Schema::table('etape_type_demande', function (Blueprint $table) {
            $table->dropColumn('is_traitement');
        });
    }
};
