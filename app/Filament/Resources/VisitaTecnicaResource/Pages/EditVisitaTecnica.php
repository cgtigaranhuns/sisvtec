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
        ];
    }

    // protected function afterSave(): void
    // {
    //     Notification::make()
    //         ->title('Visita Técnica atualizada com sucesso!')
    //         ->body('As alterações no registro foram salvas com sucesso.')
    //         ->success()
    //         ->send();
    // }
}
