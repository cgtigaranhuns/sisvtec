<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function discentes()
    {
        return $this->hasMany(Discente::class);
    }

    public function compesacaoDocente() {
        return $this->hasMany(CompensacaoDocenteNaoEnvolvido::class);
    }

    public function compesacaoTurma() {
        return $this->hasMany(CompensacaoTurmaNaoEnvolvido::class);
    }

}
