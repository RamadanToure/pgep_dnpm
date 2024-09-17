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
        Schema::create('service', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uuid')->nullable();
            $table->string('nom')->nullable();
            $table->string('sigle')->nullable();
            $table->integer('type_service_id')->index('fk_division_service');
            $table->integer('utilisateur_id')->nullable()->index('fk_division_utilisateur');
            $table->integer('parent_id')->nullable()->index('fk_service_service');
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
        Schema::dropIfExists('service');
    }
};
