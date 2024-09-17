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
        Schema::create('type_demande_type_document_etape', function (Blueprint $table) {
            $table->integer('etape_id')->index('fk_type_demande_type_document_etape');
            $table->integer('type_demande_id')->index('fk_type_demande_type_document_demande');
            $table->integer('type_document_id')->index('fk_type_demande_type_document_type');

            $table->primary(['type_demande_id', 'type_document_id', 'etape_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_demande_type_document_etape');
    }
};
