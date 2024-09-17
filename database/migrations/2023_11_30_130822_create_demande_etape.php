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
        Schema::create('demande_etape', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('uuid')->nullable();
            $table->string('recu_paiement')->nullable();
            $table->string('recu_paiement_preview')->nullable();
            $table->boolean('status')->default(false);
            $table->integer("ordre")->nullable();
            $table->boolean('is_mobile_paiement')->default(false);

            $table->integer('type_paiement_id')->nullable()->index('fk_type_paiement_demande_etape');
            $table->integer('etape_id')->index('fk_etape_demande_etape');
            $table->integer('demande_id')->nullable()->index('fk_demande_demande_etape');

            $table->timestamps();
        });

        Schema::table('demande_etape', function (Blueprint $table) {
            $table->foreign(['etape_id'], 'fk_etape_demande_etape')->references(['id'])->on('etape')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['demande_id'], 'fk_demande_demande_etape')->references(['id'])->on('demande')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign(['type_paiement_id'], 'fk_type_paiement_demande_etape')->references(['id'])->on('type_paiement')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_etape', function (Blueprint $table) {
            $table->dropForeign('fk_etape_demande_etape');
            $table->dropForeign('fk_demande_demande_etape');
            $table->dropForeign('fk_type_paiement_demande_etape');
        });

        Schema::dropIfExists('demande_etape');
    }
};
