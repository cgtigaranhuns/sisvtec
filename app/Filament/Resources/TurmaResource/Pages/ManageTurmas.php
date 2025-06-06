<?php

namespace App\Filament\Resources\TurmaResource\Pages;

use App\Filament\Resources\TurmaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTurmas extends ManageRecords
{
    protected static string $resource = TurmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Adicionar Turma')
                ->icon('heroicon-o-plus')
                ->color('success'),
                
        ];
    }
}
