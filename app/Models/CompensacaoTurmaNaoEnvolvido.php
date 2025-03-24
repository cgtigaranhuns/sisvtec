<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompensacaoTurmaNaoEnvolvido extends Model
{
    use HasFactory;

    protected $fillable = [
        'visita_tecnica_id',
        'user_id',
        'disciplina_id',
        'turma_id',
        'data_hora_reposicao',
        'user2_id',
    ];

    public function visitaTecnica()
    {
        return $this->belongsTo(VisitaTecnica::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function disciplina()
    {  
        return $this->belongsTo(Disciplina::class);
    }

    public function turma() 
    {
        return $this->belongsTo(Turma::class);
    }
}
