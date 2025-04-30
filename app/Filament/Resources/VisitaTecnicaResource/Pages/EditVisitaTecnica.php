<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use App\Mail\TermoCompromisso;
use App\Models\VisitaTecnica;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditVisitaTecnica extends EditRecord
{
    protected static string $resource = VisitaTecnicaResource::class;

    protected static ?string $title = 'Editar Atividade Extraclasse';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            // Actions\Action::make('gerarTermoCompromisso')
            //     ->label('Gerar Termo de Compromisso')

            //     ->url(fn(VisitaTecnica $record): string => route('imprimirTermoCompromisso', $record))
            //     ->openUrlInNewTab(),
            // Actions\Action::make('enviarTermoCompromisso')
            //     ->label('Enviar Termo por Email')
            //     ->action(function (VisitaTecnica $record) {
            //         foreach ($record->discenteVisitas as $discente) {
            //             // dd($discente);
            //             Mail::to($discente->discente->email)->send(new termoCompromisso($record, $discente->discente->id));
            //         }
            //         Notification::make()
            //             ->title('Termo de compromisso enviado por email!')
            //             ->success()
            //             ->send();
            //     })
            //     ->icon('heroicon-o-envelope')
            //     ->requiresConfirmation()
            //     ->modalHeading('Enviar Termo de Compromisso')
            //     ->modalDescription('Deseja enviar o termo de compromisso para todos os discentes?')
            //     ->modalIcon('heroicon-o-envelope'),


        ];
    }

    protected function getFooterActions(): array
    {

        return [
            Actions\Action::make('submeter')
                ->label('Submeter Proposta')
                ->action(function () {
                    $this->record->status = 1;
                    $this->record->save();

                    Notification::make()
                        ->title('Proposta enviada com sucesso!')
                        ->success()
                        ->persistent()
                        ->send();
                })
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status != 1)
                ->modalHeading('Enviar Proposta')
                ->modalDescription('Tem certeza que deseja enviar a proposta?')
                ->modalIcon('heroicon-o-paper-airplane'),
        ];
    }
}
