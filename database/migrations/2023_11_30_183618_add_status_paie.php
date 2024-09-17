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
        Schema::table('paiement', function (Blueprint $table) {
            $table->integer('status')->default(0);
            $table->longText("note")->nullable();
            $table->dateTime("date_status")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiement', function (Blueprint $table) {
            $table->dropColumn(['status', 'note', 'date_status']);
        });
    }
};
