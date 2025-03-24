<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitaTecnica extends Model
{
    use HasFactory;

    protected $fillable = [

        'categoria_id',
        'sub_categoria_id',
        'custo',
        'compensacao',
        'emp_evento',
        'coordenacao_id',
        'curso_id',
        'turma_id',
        'disciplina_id',
        'professor_id',
        'srv_participante_id',
        'justificativa_servidores',
        'estado_id',
        'cidade_id',
        'data_hora_saida',
        'data_hora_retorno',
        'carga_horaria_total',
        'carga_horaria_visita',
        'conteudo_programatico',
        'qtd_estudantes',
        'hospedagem',
        'cotacao_hospedagem',
        'menor_valor_hospedagem',
        'valor_total_diarias',
        'custo_total',
        'justificativa_hospedagem',
        'status',
        'justificativa',
        'just_outra_disciplina',
        'objetivos',
        'motodologia',
        'form_avalia_aprend'

    ];

    protected $casts = [
        'disciplina_id' => 'array',
        'srv_participante_id' => 'array',
        'cotacao_hospedagem' => 'array',

    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subCategoria()
    {
        return $this->belongsTo(SubCategoria::class);
    }

    public function coordenacao()
    {
        return $this->belongsTo(Coordenacao::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class);
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class);
    }

    public function srvParticipante()
    {
        return $this->belongsTo(User::class);
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discenteVisitas()
    {
        return $this->hasMany(DiscenteVisita::class);
    }

    public function compensacaoEnvolvidos() {
        return $this->hasMany(CompensacaoEnvolvido::class);
    }

    public function compensacaoNaoEnvolvidos() {
        return $this->hasMany(CompensacaoNaoEnvolvido::class);
    }




}
