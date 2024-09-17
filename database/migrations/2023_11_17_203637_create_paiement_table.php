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
        Schema::create('paiement', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uuid', 100)->nullable();
            $table->bigInteger('montant')->nullable();
            $table->dateTime('date_paiement')->nullable();
            $table->integer('demande_id')->nullable()->index('fk_paiement_demande');
            $table->integer('type_paiement_id')->nullable()->index('fk_paiement_type_paiement');
            $table->integer('etape_id')->nullable()->index('fk_paiement_etape');
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
        Schema::dropIfExists('paiement');
    }
};
