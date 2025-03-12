<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisitaTecnica extends EditRecord
{
    protected static string $resource = VisitaTecnicaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
