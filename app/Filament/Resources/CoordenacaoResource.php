<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoordenacaoResource\Pages;
use App\Filament\Resources\CoordenacaoResource\RelationManagers;
use App\Models\Coordenacao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoordenacaoResource extends Resource
{
    protected static ?string $model = Coordenacao::class;

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Coordenações';

    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome')
                    ->required()
                    ->maxLength(50),

                Forms\Components\Select::make('user_id')
                    ->label('coordenador')
                    ->relationship('user', 'name')
                    //  ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->username} - {$record->name} - {$record->cargo->nome}")
                    ->searchable(['username', 'name'])
                    ->preload()
                    ->required(true),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Coordenador'),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail'),
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
            'index' => Pages\ManageCoordenacaos::route('/'),
        ];
    }
}
