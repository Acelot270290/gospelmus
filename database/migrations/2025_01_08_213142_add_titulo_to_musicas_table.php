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
        Schema::table('musicas', function (Blueprint $table) {
            $table->string('titulo')->after('tonalidade_id'); // Adiciona o campo `titulo`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musicas', function (Blueprint $table) {
            $table->dropColumn('titulo'); // Remove o campo `titulo` caso o rollback seja executado
        });
    }
};
