<?php

namespace App\Filament\Resources\VisitaTecnicaResource\Pages;

use App\Filament\Resources\VisitaTecnicaResource;
use App\Models\VisitaTecnica;
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
                ->modalHeading('Adicionar Atividade Extraclasse')
                ->disabled(function () {

               //     dd(VisitaTecnica::where('professor_id', auth()->user()->id)->whereNotIn('status', [4, 5])->count());
                   
                    if (VisitaTecnica::where('professor_id', auth()->user()->id)
                        ->whereNotIn('status', [4, 5])
                        ->whereRaw('TIMESTAMPDIFF(DAY, data_hora_retorno, NOW()) > 10')
                        ->count() > 0) {
                        Notification::make()
                            ->title('Atenção!')
                            ->body('Você não pode adicionar uma nova atividade, pois existe uma ou mais atividades pendentes.')
                            ->danger()
                            ->send();

                        return true;
                    } else {
                        return false;
                    }
                })




        ];
    }
}
