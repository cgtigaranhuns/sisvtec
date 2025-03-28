<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVisitaTecnicas extends ListRecords
{
    protected static string $resource = VisitaTecnicaResource::class;

    protected static ?string $title = 'Visitas Técnicas';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo')
                ->modalHeading('Criar Proposta')
        ];
    }
}
