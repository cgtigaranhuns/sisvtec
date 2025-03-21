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
        Schema::create('discentes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->string('matricula', 50);
            $table->string('email', 50);
            $table->date('data_nacimento');
            $table->string('cpf', 11);
            $table->string('rg', 10);
            $table->string('orgao_exp_rg', 10);
            $table->string('data_exp_rg', 10);
            $table->string('banco_id', 5);
            $table->string('agencia', 8);
            $table->string('conta', 10);
            $table->string('tipo_conta', 5);
            $table->string('curso_id', 10);
            $table->string('turma_id', 10);
            $table->boolean('status');
            $table->string('foto');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discentes');
    }
};
