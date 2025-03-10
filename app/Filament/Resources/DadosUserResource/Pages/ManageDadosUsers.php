<?php

namespace App\Filament\Resources\DadosUserResource\Pages;

use App\Filament\Resources\DadosUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDadosUsers extends ManageRecords
{
    protected static string $resource = DadosUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
