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
        foreach ($visitaTecnica->disciplina_id as $disciplinaId) {
            $disciplina = \App\Models\Disciplina::find($disciplinaId);
            if ($disciplina) {
                $nomeDisciplinas[] = $disciplina->nome;
            }
        }

        $nomeTurmas = [];
        foreach ($visitaTecnica->turma_id as $turmaId) {
            $turma = \App\Models\Turma::find($turmaId);
            if ($turma) {
                $nomeTurmas[] = $turma->nome;
            }
        }

        $nomeCursos = [];
        foreach ($visitaTecnica->curso_id as $cursoId) {
            $curso = \App\Models\Curso::find($cursoId);
            if ($curso) {
                $nomeCursos[] = $curso->nome;
            }
        }

        //  dd($nomeDisciplinas);

        return view('imprimir.visitaTecnica', compact('visitaTecnica', 'nomeDisciplinas', 'nomeTurmas', 'nomeCursos'));
    }

    public function imprimirRelatorioFinal($id)
    {
        $visitaTecnica = VisitaTecnica::find($id);
        $relatorioFinal = $visitaTecnica->relatorioFinalVisitaTecnica;
        // dd($relatorioFinal);
        return view('imprimir.relatorioFinal', compact('visitaTecnica', 'relatorioFinal'));
    }

    public function imprimirAtaVisitaTecnica($id)
    {
        $visitaTecnica = VisitaTecnica::find($id);
        $ataVisitaTecnica = $visitaTecnica->ataVisitaTecnica;
        return view('imprimir.ataVisitaTecnica', compact('visitaTecnica', 'ataVisitaTecnica'));
    }

    public function imprimirTermoCompromisso($id)
    {
        $visitaTecnica = VisitaTecnica::find($id);

        $pdfs = [];
        foreach ($visitaTecnica->discenteVisitas as $discente) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('imprimir.termoCompromissoEmail', compact('visitaTecnica', 'discente'));
            $fileName = 'termo_compromisso_' . $visitaTecnica->id . '-' . $discente->discente->id . '.pdf';
            $filePath = storage_path('app/public/termos_compromisso/' . $fileName);
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0777, true);
            }
            $pdf->save($filePath);
            $pdfs[] = $filePath;
        }

        return response()->json([
            'message' => 'Termos de compromisso gerados com sucesso.',
            'files' => $pdfs,
        ]);
    }
    public function downloadTermoCompromisso($id, $discente)
    {
        // $visitaTecnica = VisitaTecnica::find($id);
        // $discente = $visitaTecnica->discenteVisitas->where('discente_id', $discenteId)->first();

        // if (!$discente) {
        //     return response()->json(['message' => 'Discente não encontrado.'], 404);
        // }

        // $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('imprimir.termoCompromisso', compact('visitaTecnica', 'discente'));
        // $fileName = 'termo_compromisso_' . $visitaTecnica->id . '-' . $discente->discente->id . '.pdf';

        // return $pdf->download($fileName);

        $visitaTecnica = VisitaTecnica::find($id);
               // $discente = $visitaTecnica->discenteVisitas->first();
              //  dd($discente, $visitaTecnica);
              $discente = $visitaTecnica->discenteVisitas->where('discente_id', $discente)->first();
                return view('imprimir.termoCompromisso', compact('visitaTecnica', 'discente')); 
    }
}
//     {
//        
//     }
// }
