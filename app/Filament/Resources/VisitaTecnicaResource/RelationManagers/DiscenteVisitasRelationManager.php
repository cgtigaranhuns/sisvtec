<?php

namespace App\Filament\Resources\VisitaTecnicaResource\RelationManagers;

use App\Mail\PropostaEmail;
use App\Models\Discente;
use App\Models\DiscenteVisita;
use App\Models\Turma;
use App\Models\VisitaTecnica;
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
use App\Traits\RecalculaFinanceiro;

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
            ->defaultSort('status', 'desc')
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'warning',
                        '2' => 'info',
                        '3' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '0' => 'Pendência Financeira',
                        '1' => 'Cadastro Incompleto',
                        '2' => 'Desativado',
                        '3' => 'OK',
                    })

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
                            if ($discente) {
                                // Cria o registro de DiscenteVisita
                                DiscenteVisita::create([
                                    'discente_id' => $data['discente_id'],
                                    'visita_tecnica_id' => $livewire->ownerRecord->id,
                                    'status' => $discente->status,

                                ]);
                                if ($discente->status == 0) {
                                    Notification::make()
                                        ->title('Estudante com pendência')
                                        ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está com pendência financeira.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                } elseif ($discente->status == 1) {
                                    Notification::make()
                                        ->title('Estudante com pendência')
                                        ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está com cadastro incompleto.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                } elseif ($discente->status == 2) {
                                    Notification::make()
                                        ->title('Estudante com pendência')
                                        ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está inativo.')
                                        ->info()
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
                            if ($discente) {
                                $exists = DiscenteVisita::where('discente_id', $discente->id)
                                    ->where('visita_tecnica_id', $livewire->ownerRecord->id)
                                    ->exists();

                                if (!$exists) {
                                    DiscenteVisita::create([
                                        'discente_id' => $discente->id,
                                        'visita_tecnica_id' => $livewire->ownerRecord->id,
                                        'status' => $discente->status,
                                    ]);
                                } else {
                                    Notification::make()
                                        ->title('Estudante já incluído')
                                        ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' já está incluído na visita.')
                                        ->warning()
                                        ->persistent()
                                        ->send();
                                }
                            }

                            if ($discente->status == 0) {
                                Notification::make()
                                    ->title('Estudante com pendência')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está com pendência financeira.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            } elseif ($discente->status == 1) {
                                Notification::make()
                                    ->title('Estudante com pendência')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está com cadastro incompleto.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            } elseif ($discente->status == 2) {
                                Notification::make()
                                    ->title('Estudante com pendência')
                                    ->body('O estudante ' . $discente->nome . ' - ' . $discente->matricula . ' não foi incluído na visita, pois está inativo.')
                                    ->info()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    }),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Atualizar Status')
                    ->action(function ($livewire) {
                        $discenteVisitas = $livewire->ownerRecord->discenteVisitas;

                        foreach ($discenteVisitas as $discenteVisita) {
                            $discente = Discente::find($discenteVisita->discente_id);

                            if ($discente && $discente->status != $discenteVisita->status) {
                                $discenteVisita->status = $discente->status;
                                $discenteVisita->save();
                            }
                        }

                        Notification::make()
                            ->title('Status atualizado com sucesso!')
                            ->success()
                            ->persistent()
                            ->send();
                    })
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->disabled(fn($livewire) =>  $livewire->ownerRecord->status != 0)
                    ->modalHeading('Atualizar Status')
                    ->modalDescription('Tem certeza que deseja atualizar o status dos discentes?')
                    ->modalIcon('heroicon-o-arrow-path'),

                // Tables\Actions\Action::make('enviartermo')
                //     ->label('Enviar Termo de Compromisso')
                //     ->url(
                //         route('imprimirTermoCompromisso'),
                //         fn($record) => [
                //             'id' => $record->id,
                //         ]
                //     )
                //     ->color('success'),
                Tables\Actions\Action::make('submeter')

                    ->label(function () {
                        if ($this->ownerRecord->status > 0) {
                            return 'Proposta enviada';
                        } else {
                            return 'Submeter proposta';
                        }
                    })
                    ->action(function ($livewire) {

                        $totalDiscentes = $livewire->ownerRecord->qtd_estudantes;
                        $discentesStatusOk = $livewire->ownerRecord->discenteVisitas()->where('status', 3)->count();
                        $discentesStatusTodos = $livewire->ownerRecord->discenteVisitas()->count();

                        $livewire->ownerRecord->status = 1;
                        $livewire->ownerRecord->save();
                        Mail::to($livewire->ownerRecord->professor->email)->cc($livewire->ownerRecord->coordenacao->email)->send(new PropostaEmail($livewire->ownerRecord));
                        $livewire->redirect(route('filament.admin.resources.visita-tecnicas.index'));
                        Notification::make()
                            ->title('Proposta enviada com sucesso!')
                            ->success()
                            ->persistent()
                            ->send();
                            
                        if ($totalDiscentes != $discentesStatusOk) {
                            Notification::make()
                                ->title('ATENÇÃO: Inconsistência de dados')
                                ->body('<p style="text-align: justify;"> A quantidade de estudantes informada na proposta foi <b>' . $totalDiscentes . ' estudantes</b>, porém os estudantes ' . $discentesStatusOk . ' estudantes</b>, corriga a diferença e tente novamente.</p>')
                                ->danger()
                                ->icon('heroicon-o-exclamation-triangle')
                                ->color('danger')
                                ->persistent()
                                ->send();
                        } elseif ($discentesStatusTodos != $totalDiscentes) {
                            Notification::make()
                                ->title('ATENÇÃO: Inconsistência de dados')
                                ->body('<p style="text-align: justify;"> Não existe nenhum discente cadastrado na proposta, corriga a diferença e tente novamente.</p>')
                                ->danger()
                                ->icon('heroicon-o-exclamation-triangle')
                                ->color('danger')
                                ->persistent()
                                ->send();
                        }
                    })
                    ->color('info')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    //  ->visible(fn($livewire) => $livewire->ownerRecord->discenteVisitas()->exists() && (($livewire->ownerRecord->compesacao == true && $livewire->ownerRecord->compensacaoDocenteNaoEnvolvido()->exists() && $livewire->ownerRecord->compensacaoTurmaEnvolvido()->exists())))
                    ->visible(function ($livewire) {
                        if ($livewire->ownerRecord->discenteVisitas()->exists() && $livewire->ownerRecord->compensacao == false) {
                            return true;
                        } elseif ($livewire->ownerRecord->discenteVisitas()->exists() && $livewire->ownerRecord->compensacao == true) {
                            if ($livewire->ownerRecord->compensacaoDocenteNaoEnvolvido()->exists() && $livewire->ownerRecord->CompensacaoTurmaNaoEnvolvido()->exists()) {
                                return true;
                            } else {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    })
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
                Tables\Actions\Action::make('imprimir')
                    ->label('Termo de Compromisso')
                    ->url(fn($livewire, $record): string => route('downloadTermoCompromisso', [$livewire->ownerRecord->id, $record->discente->id]))
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->openUrlInNewTab(),
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
