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
        Schema::table('role_permission', function (Blueprint $table) {
            $table->foreign(['permission_id'], 'fk_role_permission_permission')->references(['id'])->on('permission')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['role_id'], 'fk_role_permission_role')->references(['id'])->on('role')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_permission', function (Blueprint $table) {
            $table->dropForeign('fk_role_permission_permission');
            $table->dropForeign('fk_role_permission_role');
        });
    }
};
