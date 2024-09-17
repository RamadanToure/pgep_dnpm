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
        Schema::table('type_demande_type_document_etape', function (Blueprint $table) {
            $table->foreign(['type_demande_id'], 'fk_type_demande_type_document_demande')->references(['id'])->on('type_demande')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['type_document_id'], 'fk_type_demande_type_document_type')->references(['id'])->on('type_document')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['etape_id'], 'fk_type_demande_type_document_etape')->references(['id'])->on('etape')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('type_demande_type_document', function (Blueprint $table) {
            $table->dropForeign('fk_type_demande_type_document_demande');
            $table->dropForeign('fk_type_demande_type_document_etape');
            $table->dropForeign('fk_type_demande_type_document_type');
        });
    }
};
