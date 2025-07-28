<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscenteVisitaResource\Pages;
use App\Filament\Resources\DiscenteVisitaResource\RelationManagers;
use App\Models\Discente;
use App\Models\DiscenteVisita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class DiscenteVisitaResource extends Resource
{
    protected static ?string $model = DiscenteVisita::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Meus Dados';

    protected static ?string $navigationLabel = 'Minhas Atividades Extraclasse';

    protected static ?int $navigationSort = 3;



    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User */
        $authUser = auth()->user();

        if ($authUser->hasRole('Estudantes')) {
            // dd($authUser->username);
            return static::getModel()::query()
                ->whereHas('discente', fn(Builder $query) => $query->where('matricula', $authUser->username));
        }
        //Add a valid condition or remove this block if not needed
        else {
            return static::getModel()::query(); // Default return for other cases
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visitaTecnica.status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'info',
                        '2' => 'success',
                        '3' => 'danger',
                        '4' => 'warning',
                        '5' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '0' => 'Cadastrada',
                        '1' => 'Submetida',
                        '2' => 'Aprovada',
                        '3' => 'Reprovada',
                        '4' => 'Financeiro',
                        '5' => 'Finalizada',
                    }),
                Tables\Columns\TextColumn::make('visitaTecnica.emp_evento')
                    ->label('Atividade Extraclasse')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('discente.nome')
                    ->label('Discente')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('discente.matricula')
                    ->label('Matrícula'),
                Tables\Columns\TextColumn::make('visitaTecnica.data_hora_saida')
                    ->label('Data/Hora Saída')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('visitaTecnica.data_hora_retorno')
                    ->label('Data/Hora Retorno')
                    ->dateTime('d/m/Y H:i'),
                

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('imprimir')
                    ->label('Termo de Compromisso')
                    ->url(fn($livewire, $record): string => route('downloadTermoCompromisso', [$record->visitaTecnica->id, $record->discente->id]))
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //  Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscenteVisitas::route('/'),
            'create' => Pages\CreateDiscenteVisita::route('/create'),
            'edit' => Pages\EditDiscenteVisita::route('/{record}/edit'),
        ];
    }
}
