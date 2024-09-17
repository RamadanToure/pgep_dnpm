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
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->boolean('is_deleted')->nullable();
            $table->string('photo')->nullable();
            $table->string('adresse')->nullable();
            $table->boolean('is_root')->default(false);
            $table->boolean('is_valide')->default(false);
            $table->string('status_compte')->default(false);
            $table->string('genre')->default('Masculin');
            $table->timestamp('email_verified_at')->nullable();

            $table->string('token_update_password')->nullable();
            $table->string('date_validated_token_password')->nullable();
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->boolean('is_deleted')->nullable();
            $table->string('photo')->nullable();
            $table->string('adresse')->nullable();
            $table->boolean('is_root')->default(false);
            $table->boolean('is_valide')->default(false);
            $table->string('status_compte')->default(false);
            $table->string('genre')->default('Masculin');
            $table->timestamp('email_verified_at')->nullable();

            $table->string('token_update_password')->nullable();
            $table->string('date_validated_token_password')->nullable();
            $table->rememberToken();
        });
    }
};
