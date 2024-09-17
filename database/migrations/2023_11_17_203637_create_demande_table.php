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
        Schema::create('demande', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uuid', 100)->nullable();
            $table->integer('etape_id')->index('fk_demande_etape');
            $table->integer('type_demande_id')->index('fk_demande_type_demande');
            $table->integer('utilisateur_id')->nullable()->index('fk_demande_utilisateur');
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
        Schema::dropIfExists('demande');
    }
};
