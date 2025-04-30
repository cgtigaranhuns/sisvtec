<?php

namespace App\Filament\Resources\VisitaTecnicaResource\RelationManagers;

use App\Mail\PropostaEmail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class CompensacaoDocenteNaoEnvolvidoRelationManager extends RelationManager
{
    protected static string $relationship = 'CompensacaoDocenteNaoEnvolvido';

    protected static ?string $title = 'Plano de Compensação - Docente';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('visita_tecnica_id')
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->hint('Pesquise por nome ou matrícula')
                    ->label('Professor')
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->searchable(['name', 'username'])
                    ->required(),
                Forms\Components\Select::make('disciplina_id')
                    ->label('Disciplina')
                    ->relationship('disciplina', 'nome')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('turma_id')
                    ->label('Turma')
                    ->relationship('turma', 'nome')
                    ->searchable()
                    ->required(),
                Forms\Components\DateTimePicker::make('data_hora_reposicao')
                    ->label('Data e hora da reposição')
                    ->seconds(false)
                    ->required(),
                Forms\Components\Select::make('user2_id')
                    ->hint('Pesquise por nome ou matrícula')
                    ->label('Professor que vai assumir a turma')
                    ->relationship(name: 'user2', titleAttribute: 'name')
                    ->searchable(['name', 'username'])
                    ->required(),



            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visita_tecnica_id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Professor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('disciplina.nome')
                    ->label('Disciplina')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('turma.nome')
                    ->label('Turma')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_hora_reposicao')
                    ->label('Data e hora da reposição'),
                Tables\Columns\TextColumn::make('user2.name')
                    ->label('Professor que vai assumir a turma')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Adicionar Compensação')
                    ->icon('heroicon-o-plus')
                    ->disabled(function () {
                        return $this->ownerRecord->status != 0;
                    })
                    ->label('Adicionar Compensação'),
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
                        $discentesStatusPendentes = $livewire->ownerRecord->discenteVisitas()->where('status', '!=', 3)->count();

                        if ($totalDiscentes != $discentesStatusOk && $discentesStatusTodos == $discentesStatusPendentes) {
                            $livewire->ownerRecord->status = 1;
                            $livewire->ownerRecord->save();
                            Mail::to($livewire->ownerRecord->professor->email)->cc($livewire->ownerRecord->coordenacao->email)->send(new PropostaEmail($livewire->ownerRecord));
                            $livewire->redirect(route('filament.admin.resources.visita-tecnicas.index'));
                            Notification::make()
                                ->title('Proposta enviada com sucesso!')
                                ->success()
                                ->persistent()
                                ->send();
                            Notification::make()
                                ->title('ATENÇÃO: Inconsistência de dados')
                                ->body('<p style="text-align: justify;"> A quantidade de estudantes informada na proposta, foi de <b>' . $totalDiscentes . '</b>, porém há' . $discentesStatusPendentes . '</b>estudantes que estão com status de pendência. Por favor, pedimos que informe aos estudantes para regularizar a situação..</p>')
                                ->danger()
                                ->icon('heroicon-o-exclamation-triangle')
                                ->color('danger')
                                ->persistent()
                                ->send();
                        } elseif ($discentesStatusTodos != $totalDiscentes) {
                            Notification::make()
                                ->title('Proposta NÃO enviada!')
                                ->success()
                                ->persistent()
                                ->send();
                            Notification::make()
                                ->title('ATENÇÃO: Inconsistência de dados')
                                ->body('<p style="text-align: justify;"> A quantidade de estudantes informada na proposta, foi de <b>' . $totalDiscentes . ',</b> porém após a inclusão dos nomes verificou-se que ha<b> ' .$discentesStatusTodos. ' </b>estudantes incluídos. Favor corrigir a diferença e tentar novamente.</p>')
                                ->danger()
                                ->icon('heroicon-o-exclamation-triangle')
                                ->color('danger')
                                ->persistent()
                                ->send();
                        }
                        else {
                            $livewire->ownerRecord->status = 1;
                            $livewire->ownerRecord->save();
                            Mail::to($livewire->ownerRecord->professor->email)->cc($livewire->ownerRecord->coordenacao->email)->send(new PropostaEmail($livewire->ownerRecord));
                            $livewire->redirect(route('filament.admin.resources.visita-tecnicas.index'));
                            Notification::make()
                                ->title('Proposta enviada com sucesso!')
                                ->success()
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
