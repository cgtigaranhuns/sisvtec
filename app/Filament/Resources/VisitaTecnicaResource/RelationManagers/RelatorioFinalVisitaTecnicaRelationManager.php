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

    protected static ?string $title = 'Relatório Final';

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
                Tables\Actions\CreateAction::make()
                    ->label('Adicionar Relatório Final')
                    ->modalHeading('Adicionar Relatório Final')
                    ->icon('heroicon-o-plus')
                    ->disabled(function () {
                        return $this->ownerRecord->status == 2;
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->disabled(function () {
                        return $this->ownerRecord->status == 2;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->disabled(function () {
                        return $this->ownerRecord->status == 2;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->disabled(function () {
                            return $this->ownerRecord->status == 2;
                        }),
                ]),
            ]);
    }
}
