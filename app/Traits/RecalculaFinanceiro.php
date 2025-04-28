<?php

namespace App\Traits;

use App\Models\Config;
use Illuminate\Support\Carbon;


trait RecalculaFinanceiro
{
    public static function recalculaFinanceiro($visitaTecnica)
    {
        $qtdEstudantes =   $visitaTecnica->discenteVisitas()->where('status', 3)->count();
        $dataHoraSaida = $visitaTecnica->get('data_hora_saida');
        $dataHoraRetorno = $visitaTecnica->get('data_hora_retorno');

      //  dd($dataHoraSaida, $dataHoraRetorno);

        if ($visitaTecnica->qtd_estudantes != $qtdEstudantes) {
            // CALCULA DIAS DE VIAGEM
            $valorMeiaDiaria = Config::first()->valor_meia_diaria;
            $saida = Carbon::parse($dataHoraSaida)->format('Y-m-d');
            $retorno = Carbon::parse($dataHoraRetorno)->format('Y-m-d');
            $totalHoras = Carbon::parse($retorno)->diffInHours(Carbon::parse($saida)->startOfDay());
            $days = floor($totalHoras / 24);

            if ($days < 1) {
                $valorDiarias = ((float)$qtdEstudantes * (float)$valorMeiaDiaria);
            } elseif ($days >= 1 && $days < 2) {
                $valorDiarias = ((float)$qtdEstudantes * ((float)$valorMeiaDiaria * 3));
            } elseif ($days >= 2) {
                $valorDiarias = (float)((float)$qtdEstudantes * ((((float)$valorMeiaDiaria * 2) * (float)$days) + (float)$valorMeiaDiaria));
            }

            $set('valor_total_diarias', $valorDiarias);
            $set('custo_total', ($valorDiarias + $get('menor_valor_hospedagem')));
            $visitaTecnica->update([
                'qtd_estudantes' => $qtdEstudantes,
                'valor_total_diarias' => $valorDiarias,
                'custo_total' => ($valorDiarias + $visitaTecnica->menor_valor_hospedagem),
                
            ]);
        }

        // $valorMeiaDiaria = Config::first()->valor_meia_diaria;

        // $dataHoraSaida = $get('data_hora_saida');
        // $dataHoraRetorno = $get('data_hora_retorno');


    }
}
