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
        Schema::table('demande', function (Blueprint $table) {
            $table->foreign(['etape_id'], 'fk_demande_etape')->references(['id'])->on('etape')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['type_demande_id'], 'fk_demande_type_demande')->references(['id'])->on('type_demande')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['utilisateur_id'], 'fk_demande_utilisateur')->references(['id'])->on('utilisateur')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demande', function (Blueprint $table) {
            $table->dropForeign('fk_demande_etape');
            $table->dropForeign('fk_demande_type_demande');
            $table->dropForeign('fk_demande_utilisateur');
        });
    }
};
