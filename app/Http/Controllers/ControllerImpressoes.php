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

        $nomeTurmas = [];
        foreach($visitaTecnica->turma_id as $turmaId){
            $turma = \App\Models\Turma::find($turmaId);
            if ($turma) {
            $nomeTurmas[] = $turma->nome;
            }
        }

      //  dd($nomeDisciplinas);

        return view('imprimir.visitaTecnica', compact('visitaTecnica','nomeDisciplinas','nomeTurmas'));
    }

    public function imprimirRelatorioFinal($id)
    {
        $visitaTecnica = VisitaTecnica::find($id);
        $relatorioFinal = $visitaTecnica->relatorioFinalVisitaTecnica;
       // dd($relatorioFinal);
        return view('imprimir.relatorioFinal', compact('visitaTecnica','relatorioFinal'));
    }

    public function imprimirAtaVisitaTecnica($id)
    {
        $visitaTecnica = VisitaTecnica::find($id);
        $ataVisitaTecnica = $visitaTecnica->ataVisitaTecnica;
        return view('imprimir.ataVisitaTecnica', compact('visitaTecnica','ataVisitaTecnica'));
    }
}
