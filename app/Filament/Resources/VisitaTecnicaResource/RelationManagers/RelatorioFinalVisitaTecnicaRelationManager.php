<?php

namespace App\Filament\Resources\VisitaTecnicaResource\RelationManagers;

use Faker\Core\File;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;


class RelatorioFinalVisitaTecnicaRelationManager extends RelationManager
{
    protected static string $relationship = 'RelatorioFinalVisitaTecnica';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RichEditor::make('descricao')
                    ->required()
                    ->columnSpanFull()
                    ->label('Descrição'),
                RichEditor::make('ocorrencia')
                    ->required(false)
                    ->columnSpanFull()
                    ->label('Ocorrência'),




            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visita_tecnica_id')
            ->columns([
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Descrição')
                    ->wrap()
                    ->html(),
                Tables\Columns\TextColumn::make('ocorrencia')
                    ->label('Ocorrência')
                    ->wrap()
                    ->html(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
