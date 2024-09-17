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
        Schema::table('type_demande', function (Blueprint $table) {
            $table->foreign(['service_id'], 'fk_type_demande_division')->references(['id'])->on('service')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('type_demande', function (Blueprint $table) {
            $table->dropForeign('fk_type_demande_division');
        });
    }
};
