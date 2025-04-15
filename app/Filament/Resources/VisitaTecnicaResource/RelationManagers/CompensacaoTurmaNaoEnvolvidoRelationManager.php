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

class CompensacaoTurmaNaoEnvolvidoRelationManager extends RelationManager
{
    protected static string $relationship = 'CompensacaoTurmaNaoEnvolvido';

    protected static ?string $title = ' Plano de Compensação - Turmas';

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
                    ->dateTime('d/m/Y H:i')
                    ->label('Data e hora da reposição'),

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
                    ->label('Submeter Proposta')
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
                    ->visible(fn ($livewire) => $livewire->ownerRecord->discenteVisitas()->exists())
                    ->disabled(fn ($livewire) =>  $livewire->ownerRecord->status != 0)
                    ->modalHeading('Enviar Proposta')
                    ->modalDescription('Tem certeza que deseja enviar a proposta?')
                    ->modalIcon('heroicon-o-paper-airplane'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(function () {
                        return $this->ownerRecord->status != 0;
                    })
                    ->label('Editar'),
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
