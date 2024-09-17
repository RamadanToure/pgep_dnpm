<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_demande_service', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string("uuid");
            $table->string("file")->nullable();
            $table->longText("note")->nullable();
            $table->boolean('status')->default(false);
            $table->integer('demande_id')->nullable();
            $table->integer('service_expediteur_id')->nullable();
            $table->integer('service_destinataire_id')->nullable();
            $table->timestamps();

            $table->foreign('service_expediteur_id')->references('id')->on('service');
            $table->foreign('service_destinataire_id')->references('id')->on('service');
            $table->foreign('demande_id')->references('id')->on('demande');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_demande_service');
    }
};
