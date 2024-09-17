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
        Schema::table('service_demande_service', function (Blueprint $table) {
            $table->dateTime("date_transmission")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_demande_service', function (Blueprint $table) {
            $table->dropColumn('date_transmission');
        });
    }
};
