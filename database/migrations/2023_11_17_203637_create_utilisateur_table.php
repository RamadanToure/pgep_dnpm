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
        Schema::create('utilisateur', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uuid', 100)->nullable();
            $table->string('prenom', 100)->nullable();
            $table->string('nom', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telephone', 11)->nullable();
            $table->string('password')->nullable();
            $table->integer('role_id')->nullable()->index('fk_utilisateur_role');
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
        Schema::dropIfExists('utilisateur');
    }
};
