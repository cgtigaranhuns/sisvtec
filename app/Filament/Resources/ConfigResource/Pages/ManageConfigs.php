<?php

namespace App\Filament\Resources\ConfigResource\Pages;

use App\Filament\Resources\ConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConfigs extends ManageRecords
{
    protected static string $resource = ConfigResource::class;

    protected static null|string $title = 'Configurações';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->modalHeading('Criar Parâmetro')
            //     ->label('Nova Parametrização'),
        ];
    }
}
