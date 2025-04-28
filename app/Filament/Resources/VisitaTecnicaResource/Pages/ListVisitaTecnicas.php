<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use Filament\Actions;
use Filament\Actions\Modal\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListVisitaTecnicas extends ListRecords
{
    protected static string $resource = VisitaTecnicaResource::class;

    protected static ?string $title = 'Atividades Extraclasses';
    


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Adicionar Atividade')
                ->icon('heroicon-o-plus')
                ->color('success')
                ->modalHeading('Adicionar Atividade Extraclasse'),
           
                
               
        ];
    }
}
