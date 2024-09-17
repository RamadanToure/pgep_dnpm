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
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->foreign(['role_id'], 'fk_utilisateur_role')->references(['id'])->on('role')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        Schema::table('service', function (Blueprint $table) {
            $table->foreign(['parent_id'], 'fk_service_service')->references(['id'])->on('service')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('utilisateur', function (Blueprint $table) {
            $table->dropForeign('fk_utilisateur_role');
        });

        Schema::table('service', function (Blueprint $table) {
            $table->dropForeign('fk_service_service');
        });
    }
};
