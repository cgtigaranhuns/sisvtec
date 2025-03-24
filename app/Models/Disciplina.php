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

    public function compensacaoEnvolvidos(){
        return $this->hasMany(CompensacaoEnvolvido::class);
    }

}
