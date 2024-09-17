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
        Schema::create('etape_type_demande', function (Blueprint $table) {
            $table->integer('etape_id');
            $table->integer('type_demande_id')->index('fk_etape_type_demande_type_demande');
            $table->string('uuid', 100)->nullable();
            $table->integer('ordre');
            $table->integer('type_paiement_id')->nullable()->index('fk_etape_type_demande');
            $table->primary(['etape_id', 'type_demande_id']);
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
        Schema::dropIfExists('etape_type_demande');
    }
};
