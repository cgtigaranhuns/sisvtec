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
        Schema::create('visita_tecnicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('sub_categoria_id')->constrained('sub_categorias');
            $table->boolean('custo');
            $table->boolean('compensacao');
            $table->string('emp_evento');
            $table->foreignId('coordenacao_id')->constrained('coordenacaos');
            $table->foreignId('curso_id')->constrained('cursos');
            $table->foreignId('turma_id')->constrained('turmas');
            $table->json('comp_curriculares');           
            $table->foreignId('professor_id')->constrained('users');
            $table->foreignId('srv_participante_id')->constrained('users')->nullable();
            $table->string('justificativa_servidores', 150)->nullable();
            $table->foreignId('estado_id')->constrained('estados');
            $table->foreignId('cidade_id')->constrained('cidades');
            $table->dateTime('data_hora_saida');
            $table->dateTime('data_hora_retorno');
            $table->time('carga_horaria_total');
            $table->longText('conteudo_programatico');
            $table->decimal('custo_total',10,2)->nullable();
            $table->integer('qtd_estudantes')->nullable();
            $table->boolean('hospedagem');
            $table->longText('justificativa_hospedagem')->nullable();
            $table->boolean('status');
            $table->longText('justificativa');
            $table->longText('just_outra_disciplina')->nullable();
            $table->longText('objetivos');
            $table->longText('motodologia');
            $table->longText('form_avalia_aprend');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visita_tecnicas');
    }
};
