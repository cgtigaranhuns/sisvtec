<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discente extends Model
{
    use HasFactory;

    protected $fillable = [
       
           'nome',
           'nome_social',
           'matricula',
           'email',
           'data_nascimento',
           'cpf',
           'rg',
           'orgao_exp_rg',
           'data_exp_rg',
           'banco_id',
           'agencia',
           'conta',
           'tipo_conta',
           'curso_id',
           'turma_id',
           'status',
           'foto',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
   
    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

    public function discenteVisita()
    {
        return $this->hasMany(DiscenteVisita::class);
    }


}
