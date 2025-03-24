<?php

namespace App\Filament\Resources\VisitaTecnicaResource\RelationManagers;

use App\Models\Discente;
use App\Models\DiscenteVisita;
use App\Models\Turma;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscenteVisitasRelationManager extends RelationManager
{
    protected static string $relationship = 'discenteVisitas';

    protected static ?string $title = 'Discentes da Visita';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('discente_id')
                    ->relationship('discente', 'nome')
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visita_tecnica_id')
            ->columns([
                Tables\Columns\TextColumn::make('discente.nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('discente.matricula')
                    ->label('Matrícula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discente.turma.nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('falta')
                    ->label('Faltou?')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable()
                    ->afterStateUpdated(function ($record, $state) {
                        $record->falta = $state;
                        $record->save();

                        if ($state) {
                            $discente = Discente::find($record->discente_id);
                            $discente->status = 0;
                            $discente->save();
                        } elseif (!$state) {
                            $discente = Discente::find($record->discente_id);
                            $discente->status = 3;
                            $discente->save();
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make('add')
                    ->label('Adicionar Discente')
                    ->icon('heroicon-o-user')
                    ->modalHeading('Adicionar Discente')
                    ->action(function ($livewire, array $data) {
                        $discente = Discente::find($data['discente_id']);
                        $exists = DiscenteVisita::where('discente_id', $data['discente_id'])
                            ->where('visita_tecnica_id', $livewire->ownerRecord->id)
                            ->exists();

                        if (!$exists) {
                            DiscenteVisita::create([
                                'discente_id' => $data['discente_id'],
                                'visita_tecnica_id' => $livewire->ownerRecord->id,
                            ]);
                        } else {
                            Notification::make()
                                ->title('Estudante já incluído')
                                ->body('O estudante ' . $discente->nome . '-' . $discente->matricula . ' já está incluído na visita.')
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                    }),
                Tables\Actions\CreateAction::make('addMais')
                    ->label('Adicionar Discente por Turma')
                    ->modalHeading('Adicionar todos os discentes da turma')
                    ->icon('heroicon-o-user-group')
                    ->model(Turma::class)
                    ->form([
                        Select::make('turma_id')
                            ->relationship('turma', 'nome')
                            ->required(),

                        // ...
                    ])
                    ->mutateFormDataUsing(function ($data) {
                        return $data;
                    })
                    ->action(function ($livewire, array $data) {
                        //  dd($livewire);
                        $turma = Turma::find($data['turma_id']);
                        $discentes = $turma->discentes;
                        foreach ($discentes as $discente) {
                            if ($discente->status == 3) {
                                $exists = DiscenteVisita::where('discente_id', $discente->id)
                                    ->where('visita_tecnica_id', $livewire->ownerRecord->id)
                                    ->exists();

                                if (!$exists) {
                                    DiscenteVisita::create([
                                        'discente_id' => $discente->id,
                                        'visita_tecnica_id' => $livewire->ownerRecord->id,
                                    ]);
                                } else {
                                    Notification::make()
                                        ->title('Estudante já incluído')
                                        ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' já está incluído na visita.')
                                        ->warning()
                                        ->persistent()
                                        ->send();
                                }
                            } else {
                                Notification::make()
                                    ->title('Estudante não incluído')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está inativo.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
