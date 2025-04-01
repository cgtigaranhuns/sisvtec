<?php

namespace App\Http\Controllers;

use App\Models\VisitaTecnica;
use Illuminate\Http\Request;

class ControllerImpressoes extends Controller
{
    public function imprimirVisitaTecnica($id)
    {
        $visitaTecnica = VisitaTecnica::find($id);

        $nomeDisciplinas = [];
        foreach($visitaTecnica->disciplina_id as $disciplinaId){
            $disciplina = \App\Models\Disciplina::find($disciplinaId);
            if ($disciplina) {
            $nomeDisciplinas[] = $disciplina->nome;
            }
        }

      //  dd($nomeDisciplinas);

        return view('imprimir.visitaTecnica', compact('visitaTecnica','nomeDisciplinas'));
    }

    public function imprimirRelatorioFinal($id)
    {
        $visitaTecnica = VisitaTecnica::find($id);
        $relatorioFinal = $visitaTecnica->relatorioFinalVisitaTecnica;
       // dd($relatorioFinal);
        return view('imprimir.relatorioFinal', compact('visitaTecnica','relatorioFinal'));
    }
}
