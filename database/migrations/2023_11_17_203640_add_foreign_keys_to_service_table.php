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
        Schema::table('service', function (Blueprint $table) {
            $table->foreign(['type_service_id'], 'fk_division_service')->references(['id'])->on('type_service')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['utilisateur_id'], 'fk_division_utilisateur')->references(['id'])->on('utilisateur')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service', function (Blueprint $table) {
            $table->dropForeign('fk_division_service');
            $table->dropForeign('fk_division_utilisateur');
        });
    }
};
