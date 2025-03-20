<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscenteResource\Pages;
use App\Filament\Resources\DiscenteResource\RelationManagers;
use App\Models\Discente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscenteResource extends Resource
{
    protected static ?string $model = Discente::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('matricula')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('data_nacimento')
                    ->required(),
                Forms\Components\TextInput::make('cpf')
                    ->required()
                    ->maxLength(11),
                Forms\Components\TextInput::make('rg')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('orgao_exp_rg')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('data_exp_rg')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('banco_id')
                    ->required()
                    ->maxLength(5),
                Forms\Components\TextInput::make('agencia')
                    ->required()
                    ->maxLength(8),
                Forms\Components\TextInput::make('conta')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('tipo_conta')
                    ->required()
                    ->maxLength(5),
                Forms\Components\TextInput::make('curso_id')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('turma_id')
                    ->required()
                    ->maxLength(10),
                Forms\Components\Toggle::make('situacao')
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('matricula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_nacimento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rg')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orgao_exp_rg')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data_exp_rg')
                    ->searchable(),
                Tables\Columns\TextColumn::make('banco_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('conta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_conta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('curso_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('turma_id')
                    ->searchable(),
                Tables\Columns\IconColumn::make('situacao')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDiscentes::route('/'),
        ];
    }
}
