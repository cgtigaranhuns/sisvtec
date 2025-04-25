<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatorioFinal extends Model
{
    use HasFactory;

    protected $fillable = [
        'visita_tecnica_id',
        'descricao',
        'ocorrencia',
        'fotos',
        'conferido',
        
    ];

    protected $casts = [
        'fotos' => 'array',
    ];

   

    public function visitaTecnica()
    {
        return $this->belongsTo(VisitaTecnica::class);
    }


}
