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

    public function compensacaoEnvolvidos(){
        return $this->hasMany(CompensacaoEnvolvido::class);
    }

}
