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
        Schema::table('paiement', function (Blueprint $table) {
            $table->foreign(['demande_id'], 'fk_paiement_demande')->references(['id'])->on('demande')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['etape_id'], 'fk_paiement_etape')->references(['id'])->on('etape')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['type_paiement_id'], 'fk_paiement_type_paiement')->references(['id'])->on('type_paiement')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paiement', function (Blueprint $table) {
            $table->dropForeign('fk_paiement_demande');
            $table->dropForeign('fk_paiement_etape');
            $table->dropForeign('fk_paiement_type_paiement');
        });
    }
};
