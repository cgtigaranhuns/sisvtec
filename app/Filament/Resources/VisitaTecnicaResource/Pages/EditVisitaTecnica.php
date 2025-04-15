<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVisitaTecnica extends EditRecord
{
    protected static string $resource = VisitaTecnicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            // Removed from header actions as requested
        ];
    }

    protected function getFooterWidgets(): array
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
