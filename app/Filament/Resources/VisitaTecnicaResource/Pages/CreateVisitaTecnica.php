<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use App\Mail\PropostaEmail;
use App\Mail\VisitaTecnicaMailable;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateVisitaTecnica extends CreateRecord
{
    protected static string $resource = VisitaTecnicaResource::class;

    // protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    // {
    //     $record = parent::handleRecordCreation($data);
    //     $this->afterCreate($record);
    //     return $record;
    // }

    protected function afterCreate(): void
    {
       
        Notification::make()
            ->title('Proposta criada com sucesso e enviada notificação para o coordenador!')
            ->body('Agora é necessário adicionar os estudantes que irão para Visita Técnica.<br>
                    Preenche os demais formulários abaixo, caso necessário:<br>
                    <b>1 - Discente da Visitas</b><br> 
                    <b>2 - Plano de Compensação - Docente</b><br>
                    <b>3 - Plano de Compensação - Turma</b><br>  
                   ')
            ->success()
            ->persistent()
            ->send();

          //  Mail::to($record->professor->email)->send(new PropostaEmail($record));
    }
}
