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
        Schema::create('relatorio_finals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visita_tecnica_id')->constrained('visita_tecnicas');
            $table->longText('descricao');
            $table->longText('ocorrencia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorio_finals');
    }
};
