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
        Schema::create('document', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('type_document_id')->index('fk_document_type_document');
            $table->string('file')->nullable();
            $table->string('preview')->nullable();
            $table->integer('demande_id')->nullable()->index('fk_document_demande');
            $table->integer('etape_id')->nullable()->index('fk_document_etape');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document');
    }
};
