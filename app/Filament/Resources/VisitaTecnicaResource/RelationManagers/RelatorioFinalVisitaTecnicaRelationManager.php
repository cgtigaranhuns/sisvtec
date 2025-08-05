<?php

namespace App\Filament\Resources\VisitaTecnicaResource\RelationManagers;

use App\Mail\PropostaStatusEmail;
use App\Models\Config;
use Faker\Core\File;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class RelatorioFinalVisitaTecnicaRelationManager extends RelationManager
{
    protected static string $relationship = 'RelatorioFinalVisitaTecnica';

    protected static ?string $title = 'Relatório Final';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('conferido')
                    ->label('Declaro que a lista de estudantes foi conferido!')
                    ->live()
                    ->required()
                    ->default(false)
                    ->inline()
                    ->columnSpanFull(),
                RichEditor::make('descricao')
                    ->required()
                    ->disableToolbarButtons([
                        'attachFiles',
                    ])
                    ->hidden(fn(Get $get) => $get('conferido') == false)
                    ->columnSpanFull()
                    ->label('Descrição'),
                RichEditor::make('ocorrencia')
                    ->required(false)
                    ->disableToolbarButtons([
                        'attachFiles',
                    ])
                    ->hidden(fn(Get $get) => $get('conferido') == false)
                    ->columnSpanFull()
                    ->label('Ocorrência'),
                FileUpload::make('fotos')
                    ->label('Fotos')
                    ->directory('fotos_relatorio_final')
                    ->image()
                    ->maxFiles(5)
                   // ->minSize(512)
                    ->maxSize(2048)
                    ->openable()
                    ->columnSpanFull()
                    ->panelLayout('grid')
                    ->multiple(),





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
                    ->disabled(function ($livewire) {
                        return $livewire->ownerRecord->status != 2 or $livewire->ownerRecord->data_hora_retorno > now();
                    }),
                Tables\Actions\Action::make('relatorio')
                    ->label(function () {
                        if ($this->ownerRecord->status > 3) {
                            return 'Relatório Gerado';
                        } else {
                            return 'Gerar Relatório';
                        }
                    })
                    ->action(function ($livewire) {
                        $livewire->ownerRecord->status = 4;
                        $livewire->ownerRecord->save();

                        Notification::make()
                            ->title('Relatório gerado com sucesso!')
                            ->body('Acesse a lista de atividades extraclases para visualizar o relatório gerado.')
                            ->icon('heroicon-o-check-circle')
                            ->success()
                            ->persistent()
                            ->send();
                        Mail::to($livewire->ownerRecord->professor->email)->cc(Config::first()->email_financeiro)->send(new PropostaStatusEmail($livewire->ownerRecord));
                        $livewire->redirect(route('filament.admin.resources.visita-tecnicas.index'));
                    })
                    ->color('info')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn($livewire) => $livewire->ownerRecord->data_hora_retorno < now() && $livewire->ownerRecord->RelatorioFinalVisitaTecnica()->exists())
                    ->disabled(fn($livewire) =>  $livewire->ownerRecord->status == 4)
                    ->modalHeading('Gerar Relatório')
                    ->modalDescription('Tem certeza que deseja gerar o relatório?')
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
