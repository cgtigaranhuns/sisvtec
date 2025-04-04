<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVisitaTecnica extends CreateRecord
{
    protected static string $resource = VisitaTecnicaResource::class;

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Proposta criada com sucesso e enviada para notificação para o coordenador!')
            ->body('Agora é necessário adicionar os dicentes que irão para Visita Técnica.<br>
                    Preenche os formulários abaixo:<br>
                    <b>1 - Discente da Visitas</b><br>, 
                    <b>2 - Plano de Compensação - Docente</b><br>
                    <b>3 - Plano de Compensação - Turma</b><br>  
                    caso necessário.')
            ->success()
            ->persistent()
            ->send();
    }
}
