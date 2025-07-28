<?php

namespace App\Filament\Resources\DiscenteVisitaResource\Pages;

use App\Filament\Resources\DiscenteVisitaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiscenteVisita extends EditRecord
{
    protected static string $resource = DiscenteVisitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\DeleteAction::make(),
        ];
    }
}
