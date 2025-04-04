<?php

namespace App\Filament\Resources\VisitaTecnicaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
