<?php

namespace App\Filament\Resources\VisitaTecnicaResource\RelationManagers;

use App\Mail\PropostaEmail;
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
use Illuminate\Support\Facades\Mail;

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
            ->defaultGroup('discente.turma.nome')
            ->columns([
                Tables\Columns\TextColumn::make('discente.nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('discente.matricula')
                    ->label('Matrícula')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('discente.turma.nome')
                //     ->sortable()
                //     ->searchable(),
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
                    ->disabled(function () {
                        return $this->ownerRecord->status != 0;
                    })
                    ->icon('heroicon-o-user')
                    ->modalHeading('Adicionar Discente')
                    ->action(function ($livewire, array $data) {
                        $discente = Discente::find($data['discente_id']);
                        $exists = DiscenteVisita::where('discente_id', $data['discente_id'])
                            ->where('visita_tecnica_id', $livewire->ownerRecord->id)
                            ->exists();

                        if (!$exists) {
                            if ($discente->status == 0) {
                                Notification::make()
                                    ->title('Estudante não pode ser incluído')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não pode ser incluído na visita, pois está com pendência financeira.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            } elseif ($discente->status == 1) {
                                Notification::make()
                                    ->title('Estudante não pode ser incluído')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não pode ser incluído na visita, pois está com cadastro incompleto.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            } elseif ($discente->status == 2) {
                                Notification::make()
                                    ->title('Estudante não pode ser incluído')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não pode ser incluído na visita, pois está inativo.')
                                    ->info()
                                    ->persistent()
                                    ->send();
                            } else {

                                // Cria o registro de DiscenteVisita
                                DiscenteVisita::create([
                                    'discente_id' => $data['discente_id'],
                                    'visita_tecnica_id' => $livewire->ownerRecord->id,
                                ]);
                                Notification::make()
                                    ->title('Estudante incluído com sucesso')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' foi incluído na visita.')
                                    ->success()
                                    ->persistent()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title('Estudante já incluído')
                                ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' já está incluído na visita.')
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                    }),
                Tables\Actions\CreateAction::make('addMais')
                    ->label('Adicionar Discente por Turma')
                    ->disabled(function () {
                        return $this->ownerRecord->status != 0;
                    })
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
                            } elseif ($discente->status == 0) {
                                Notification::make()
                                    ->title('Estudante não incluído')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está com pendência financeira.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            } elseif ($discente->status == 1) {
                                Notification::make()
                                    ->title('Estudante não incluído')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está com cadastro incompleto.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            } elseif ($discente->status == 2) {
                                Notification::make()
                                    ->title('Estudante não incluído')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está inativo.')
                                    ->info()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    }),
                Tables\Actions\Action::make('submeter')

                    ->label(function () {
                        if ($this->ownerRecord->status > 0) {
                            return 'Proposta enviada';
                        } else {
                            return 'Submeter proposta';
                        }
                    })
                    ->action(function ($livewire) {
                        $livewire->ownerRecord->status = 1;
                        $livewire->ownerRecord->save();

                        Notification::make()
                            ->title('Proposta enviada com sucesso!')
                            ->success()
                            ->persistent()
                            ->send();
                        Mail::to($livewire->ownerRecord->professor->email)->cc($livewire->ownerRecord->coordenacao->email)->send(new PropostaEmail($livewire->ownerRecord));
                        $livewire->redirect(route('filament.admin.resources.visita-tecnicas.index'));
                    })
                    ->color('info')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn($livewire) => $livewire->ownerRecord->discenteVisitas()->exists())
                    ->disabled(fn($livewire) =>  $livewire->ownerRecord->status != 0)
                    ->modalHeading('Enviar Proposta')
                    ->modalDescription('Tem certeza que deseja enviar a proposta?')
                    ->modalIcon('heroicon-o-paper-airplane'),

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(function () {
                        return $this->ownerRecord->status != 0;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->disabled(function () {
                        return $this->ownerRecord->status != 0;
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->disabled(function () {
                            return $this->ownerRecord->status != 0;
                        }),
                ]),
            ]);
    }
}
