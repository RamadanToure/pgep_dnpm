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
        Schema::create('type_demande', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uuid')->nullable();
            $table->string('nom')->nullable();
            $table->integer('service_id')->index('fk_type_demande_division');
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
        Schema::dropIfExists('type_demande');
    }
};
