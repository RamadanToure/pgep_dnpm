<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('type_paiement', function (Blueprint $table) {
            $table->foreign(['type_demande_id'], 'fk_type_paiement_type_demande')->references(['id'])->on('type_demande')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('type_paiement', function (Blueprint $table) {
            $table->dropForeign('fk_type_paiement_type_demande');
        });
    }
};
