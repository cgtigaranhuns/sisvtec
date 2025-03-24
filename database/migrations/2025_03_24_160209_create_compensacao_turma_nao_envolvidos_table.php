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
        Schema::create('compensacao_turma_nao_envolvidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visita_tecnica_id')->constrained('visita_tecnicas');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('disciplina_id')->constrained('disciplinas');
            $table->foreignId('turma_id')->constrained('turmas');
            $table->dateTime('data_hora_reposicao');
            $table->foreignId('user2_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensacao_turma_nao_envolvidos');
    }
};
