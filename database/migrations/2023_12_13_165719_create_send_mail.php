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
        Schema::create('send_mail', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('message')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->string('fichier')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();

            $table->integer('utilisateur_id');
            $table->foreign('utilisateur_id')->references('id')->on('utilisateur');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_mail');
    }
};
