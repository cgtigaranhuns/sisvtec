<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Discente;
use Illuminate\Support\Facades\DB; // Importe a classe DB
use Illuminate\Support\Facades\Log;

class AtualizarStatusDiscentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discentes:atualizar-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o status dos discentes com base em atributos vazios';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Use chunk para evitar problemas de memória com grandes quantidades de dados.
        Discente::chunk(100, function ($discentes) { // Processa os discentes em lotes de 100
            foreach ($discentes as $discente) {
                $atributos = $discente->getAttributes(); // Obtém todos os atributos do discente como um array.
                $temCampoVazio = false;
                
                foreach ($atributos as $atributo => $valor) {
                    if (($atributo !== 'nome_social' || $atributo !== 'foto') && empty($valor) && $discente->status != 0 ) { // Ignora o campo 'nome_social'
                        $temCampoVazio = true;
                        try {
                            // Inicia uma transação para garantir que a atualização seja feita de forma atômica.
                            DB::beginTransaction();
                            $discente->status = 1;
                            $discente->save();
                            DB::commit(); // Confirma a transação se tudo estiver OK.
                            $this->info("Status do discente {$discente->id} atualizado para 1."); // Adiciona feedback
                        } catch (\Exception $e) {
                            DB::rollBack(); // Desfaz a transação em caso de erro.
                            $this->error("Erro ao atualizar o status do discente {$discente->id}: {$e->getMessage()}");
                            // Logar o erro também é uma boa prática
                            Log::error("Erro ao atualizar status do discente {$discente->id}", [
                                'exception' => $e,
                                'discente_id' => $discente->id,
                            ]);
                        }
                        break; // Importante: Sai do loop interno após encontrar o primeiro atributo vazio
                    }
                }
                
                if (!$temCampoVazio && $discente->status != 0) {
                    try {
                        DB::beginTransaction();
                        $discente->status = 3;
                        $discente->save();
                        DB::commit();
                        $this->info("Status do discente {$discente->id} atualizado para 3.");
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $this->error("Erro ao atualizar o status do discente {$discente->id}: {$e->getMessage()}");
                        Log::error("Erro ao atualizar status do discente {$discente->id}", [
                            'exception' => $e,
                            'discente_id' => $discente->id,
                        ]);
                    }
                }
            }
        });

        $this->info('Comando executado com sucesso!'); // Mensagem geral de sucesso
        return 0; // Retorna 0 para indicar sucesso no Artisan
    }}