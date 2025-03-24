<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function visitaTecnica()
    {
        return $this->hasMany(VisitaTecnica::class);
    }

    public function compesacaoDocente() 
    {
        return $this->hasMany(CompensacaoDocenteNaoEnvolvido::class);
    }

    public function compesacaoTurma() {
        return $this->hasMany(CompensacaoTurmaNaoEnvolvido::class);
    }

}
