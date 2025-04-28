<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use App\Models\VisitaTecnica;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVisitaTecnica extends EditRecord
{
    protected static string $resource = VisitaTecnicaResource::class;

    protected static ?string $title = 'Editar Atividade Extraclasse';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('gerarTermoCompromisso')
            ->label('Gerar Termo de Compromisso')
            
                  ->url(fn(VisitaTecnica $record): string => route('imprimirTermoCompromisso', $record))
                ->openUrlInNewTab(),
          
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
                ->visible(fn () => $this->record->status != 1)
                ->modalHeading('Enviar Proposta')
                ->modalDescription('Tem certeza que deseja enviar a proposta?')
                ->modalIcon('heroicon-o-paper-airplane'),
        ];
                
                
    }     
    
    


    
}
