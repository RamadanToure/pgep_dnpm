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
        Schema::create('type_paiement', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uuid', 100)->nullable();
            $table->string('nom', 100)->nullable();
            $table->bigInteger('montant')->nullable();
            $table->integer('type_demande_id')->nullable()->index('fk_type_paiement_type_demande');
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
        Schema::dropIfExists('type_paiement');
    }
};
