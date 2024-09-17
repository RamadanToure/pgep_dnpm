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
        Schema::table('etape_type_demande', function (Blueprint $table) {
            $table->foreign(['type_paiement_id'], 'fk_etape_type_demande')->references(['id'])->on('type_paiement')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['etape_id'], 'fk_etape_type_demande_etape')->references(['id'])->on('etape')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['type_demande_id'], 'fk_etape_type_demande_type_demande')->references(['id'])->on('type_demande')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etape_type_demande', function (Blueprint $table) {
            $table->dropForeign('fk_etape_type_demande');
            $table->dropForeign('fk_etape_type_demande_etape');
            $table->dropForeign('fk_etape_type_demande_type_demande');
        });
    }
};
