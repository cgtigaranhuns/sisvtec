<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscenteVisita extends Model
{
    use HasFactory;

    protected $fillable = [
        'discente_id',
        'visita_tecnica_id',
        'falta',
        
        
    ];

    

    public function discente()
    {
        return $this->belongsTo(Discente::class);
    }

    public function visitaTecnica()
    {
        return $this->belongsTo(VisitaTecnica::class);
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}
