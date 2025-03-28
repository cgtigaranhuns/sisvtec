<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubCategoriaResource\Pages;
use App\Filament\Resources\SubCategoriaResource\RelationManagers;
use App\Models\SubCategoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubCategoriaResource extends Resource
{
    protected static ?string $model = SubCategoria::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $label = 'SubCategoria';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('categoria_id')
                    ->label('Categoria')
                    ->relationship('categoria', 'nome')
                    ->required(),                    
                Forms\Components\Textarea::make('nome')
                    ->autosize()
                    ->required()
                    ->maxLength(200),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('categoria.nome')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nome')
                    ->label('SubCategoria')
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
            'index' => Pages\ManageSubCategorias::route('/'),
        ];
    }
}
