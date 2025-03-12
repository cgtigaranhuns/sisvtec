<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BancoResource\Pages;
use App\Filament\Resources\BancoResource\RelationManagers;
use App\Models\Banco;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BancoResource extends Resource
{
    protected static ?string $model = Banco::class;

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $navigationIcon = 'heroicon-s-banknotes';

    protected static ?string $navigationLabel = 'Bancos';

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('numero')
                    ->label('Número')
                    ->required()
                    ->maxLength(10),
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->label('Número')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
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
            'index' => Pages\ManageBancos::route('/'),
        ];
    }
}
