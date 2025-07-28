<?php

namespace App\Filament\Resources\DiscenteVisitaResource\Pages;

use App\Filament\Resources\DiscenteVisitaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscenteVisitas extends ListRecords
{
    protected static string $resource = DiscenteVisitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
          //  Actions\CreateAction::make(),
        ];
    }
}
