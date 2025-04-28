
<?php

namespace App\Traits;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\VisitaTecnica;
use App\Models\Discente;
use Carbon\Carbon;

trait GeraTermoCompromisso
{
    public function gerarTermoCompromisso($visitaTecnicaId, array $discentes)
    {
        $visitaTecnica = VisitaTecnica::findOrFail($visitaTecnicaId);

        $links = [];
        foreach ($discentes as $discenteId) {
            $discente = Discente::findOrFail($discenteId);
            $links[] = route('termoCompromisso.view', [
                'visitaTecnicaId' => $visitaTecnica->id,
                'discenteId' => $discente->id,
            ]);
        }

        return $links;
    }
}
