<?php

namespace App\Traits;

use App\Models\Config;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

trait UpdateStatusDiscentes
{
    /**
     * Atualiza o status do discente com base no preenchimento de campos obrigatórios.
     *
     * @param Model $record O modelo do discente a ser atualizado.
     * @param array $data O array de dados submetidos do formulário.
     * @return void
     */
    public static function updateStatusDiscentes(Model $record, array $data): void
    {
        // Lista dos campos obrigatórios que devem ser preenchidos.
        $requiredFields = [
            'nome',
            'matricula',
            'contato',
            'endereco',
            'estado_id',
            'cidade_id',
            'cep',
            'email',
            'data_nascimento',
            'curso_id',
            'turma_id',
           // 'foto', // Para campos de upload de arquivo, certifique-se de que o $data['foto'] contenha o caminho ou nome do arquivo após o upload.
            'cpf',
            'rg',
            'orgao_exp_rg',
            'data_exp_rg',
            'banco_id',
            'agencia',
            'conta',
            'tipo_conta'
        ];

        if ($record->status == '3' or $record->status == '1') {
            // Flag para verificar se todos os campos obrigatórios estão preenchidos.
            $allFilled = true;
            // Array para armazenar os campos que estão faltando ou vazios, para uma notificação mais detalhada.
            $missingFields = [];

            // Itera sobre os campos obrigatórios e verifica se eles estão presentes e não vazios no array $data.
            foreach ($requiredFields as $field) {
                // Verifica se a chave existe no array $data e se o valor não é vazio.
                // empty() verifica '', 0, '0', null, false, array() vazios.
                if (!array_key_exists($field, $data) || empty($data[$field])) {
                    $allFilled = false;
                    $missingFields[] = $field; // Adiciona o campo à lista de campos ausentes.
                }
            }

            // Determina o novo status com base no preenchimento dos campos.
            $newStatus = $allFilled ? '3' : '1'; // '3' para OK, '1' para Pendente.

            // Atualiza o status do registro no banco de dados.
            // Usamos update() diretamente para persistir a mudança.
            $record->update(['status' => $newStatus]);

            // Envia uma notificação ao usuário com base no resultado.
            if ($allFilled) {
                Notification::make()
                    ->title('Status atualizado com sucesso!')
                    ->body('Todos os campos obrigatórios foram preenchidos.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Atenção: Campos obrigatórios não preenchidos!')
                    ->body('O status foi definido como "Pendente". Os seguintes campos estão faltando ou vazios: ' . implode(', ', $missingFields))
                    ->warning()
                    ->send();
            }
        }
    }}
