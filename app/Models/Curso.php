<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function discente()
    {
        return $this->hasMany(Discente::class);
    }

    public function visitaTecnica(){
        return $this->hasMany(VisitaTecnica::class);
    }
}
