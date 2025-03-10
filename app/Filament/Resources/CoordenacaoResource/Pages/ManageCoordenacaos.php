<?php

namespace App\Filament\Resources\CoordenacaoResource\Pages;

use App\Filament\Resources\CoordenacaoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCoordenacaos extends ManageRecords
{
    protected static string $resource = CoordenacaoResource::class;

    protected static null|string $title = 'Coordenacões';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nova Coordenacão'),
        ];
    }
}
