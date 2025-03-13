<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaTecnicaResource\Pages;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers;
use App\Models\DadosUser;
use App\Models\SubCategoria;
use App\Models\User;
use App\Models\VisitaTecnica;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class VisitaTecnicaResource extends Resource
{
    protected static ?string $model = VisitaTecnica::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Tipo de Visita')
                        ->schema([
                            Grid::make([
                                'xl' => 2,
                                '2xl' => 2,
                            ])->schema([
                                Forms\Components\Select::make('categoria_id')
                                    ->label('Categoria')
                                    ->relationship('categoria', 'nome')
                                    ->live()
                                    ->required(),

                                Forms\Components\Select::make('sub_categoria_id')
                                    ->label('Sub Categoria')
                                    ->options(fn(Get $get): Collection => SubCategoria::query()
                                        ->where('categoria_id', $get('categoria_id'))
                                        ->pluck('nome', 'id'))
                                    ->required(),
                                Forms\Components\ToggleButtons::make('custo')
                                    ->label('Haverá Custo?')
                                    ->required()
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\ToggleButtons::make('compesacao')
                                    ->label('Haverá Compensação?')
                                    ->required()
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\Textarea::make('emp_evento')
                                    ->columnSpan([
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->label('Empresa ou Evento')
                                    ->autosize()
                                    ->required()
                                    ->maxLength(255),
                            ]),


                        ]),
                    Wizard\Step::make('Participantes')
                        ->schema([
                            Grid::make([
                                'xl' => 3,
                                '2xl' => 3,
                            ])->schema([
                                Forms\Components\Select::make('coordenacao_id')
                                    ->label('Coordenação')
                                    ->searchable()
                                    ->relationship('coordenacao', 'nome')
                                    ->required(),
                                Forms\Components\Select::make('curso_id')
                                    ->label('Curso')
                                    ->searchable()
                                    ->relationship('curso', 'nome')
                                    ->required(),
                                Forms\Components\Select::make('turma_id')
                                    ->label('Turma')
                                    ->searchable()
                                    ->relationship('turma', 'nome')
                                    ->required(),
                                Forms\Components\Select::make('disciplina_id')
                                    ->label('Componentes Curriculares')
                                    ->relationship('disciplina', 'nome')
                                    ->searchable()
                                    ->live()
                                    ->multiple()
                                    ->required(),
                                Forms\Components\Select::make('professor_id')
                                    ->label('Professor Responsável')
                                    ->default(function () {
                                        return  auth()->user()->id;
                                    })
                                    ->searchable()
                                    ->relationship('professor', 'name')
                                    ->required(),
                                Forms\Components\Select::make('srv_participante_id')
                                    ->label('Servidores Participantes')
                                    ->relationship('srvParticipante', 'name')
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->username} - {$record->name}")
                                    ->searchable(['username', 'name'])
                                    ->multiple()                                    
                                    ->required(),
                                // Forms\Components\Select::make('srv_participante_id')
                                //     ->relationship(
                                //         name: 'srvParticipante',
                                //         modifyQueryUsing: fn(Builder $query) => $query->orderBy('name'),
                                //     )
                                //     ->getOptionLabelFromRecordUsing(function($record) {
                                //       //  dd($record->id);
                                //          $nome = $record->name;
                                //          $cargo = DadosUser::where('user_id', $record->id)->first();

                                //         // dd($cargo);
                                         
                                //          return "{$nome} - {$cargo->cargo->nome}";
                                //     })
                                //     ->label('Servidores Participantes')
                                //     ->preload()
                                //     ->searchable(['nome']),


                                Forms\Components\Fieldset::make('Justificativas')
                                    ->schema([
                                        Grid::make([
                                            'xl' => 2,
                                            '2xl' => 2,
                                        ])->schema([
                                            Forms\Components\Textarea::make('justificativa_servidores')
                                                ->label('Justificar Outros Servidores')
                                                ->hidden(fn(Get $get) => !$get('srv_participante_id'))
                                                ->required(fn(Get $get) => $get('srv_participante_id'))
                                                ->autosize()
                                                ->required()
                                                ->maxLength(150),
                                            Forms\Components\Textarea::make('just_outra_disciplina')
                                                ->label('Justificar Outras Disciplinas')
                                                ->hidden(fn(Get $get) => count($get('disciplina_id') ?? []) <= 1)
                                                ->required(fn(Get $get) => count($get('disciplina_id') ?? []) > 1)
                                                ->autosize()
                                                ->maxLength(150),
                                        ]),
                                    ]),


                            ]),
                        ]),
                    Wizard\Step::make('Local e Data')
                        ->schema([
                            Forms\Components\TextInput::make('estado_id')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('cidade_id')
                                ->required()
                                ->numeric(),
                            Forms\Components\DateTimePicker::make('data_hora_saida')
                                ->required(),
                            Forms\Components\DateTimePicker::make('data_hora_retorno')
                                ->required(),
                            Forms\Components\TextInput::make('carga_horaria_total')
                                ->required(),
                        ]),
                    Wizard\Step::make('Custos e Estudantes')
                        ->schema([
                            Forms\Components\TextInput::make('custo_total')
                                ->numeric(),
                            Forms\Components\TextInput::make('qtd_estudantes')
                                ->numeric(),
                            Forms\Components\Toggle::make('hospedagem')
                                ->required(),
                            Forms\Components\Textarea::make('justificativa_hospedagem')
                                ->columnSpanFull(),
                        ]),
                    Wizard\Step::make('Justificativa e Objetivos')
                        ->schema([
                            Forms\Components\Textarea::make('conteudo_programatico')
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\Toggle::make('status')
                                ->required(),
                            Forms\Components\Textarea::make('justificativa')
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('objetivos')
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('motodologia')
                                ->required()
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('form_avalia_aprend')
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('categoria_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sub_categoria_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('custo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('compensacao')
                    ->boolean(),
                Tables\Columns\TextColumn::make('emp_evento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('coordenacao_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('curso_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('turma_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('professor_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('srv_participante_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('justificativa_servidores')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cidade_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_hora_saida')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_hora_retorno')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('carga_horaria_total'),
                Tables\Columns\TextColumn::make('custo_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qtd_estudantes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('hospedagem')
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVisitaTecnicas::route('/'),
            'create' => Pages\CreateVisitaTecnica::route('/create'),
            'edit' => Pages\EditVisitaTecnica::route('/{record}/edit'),
        ];
    }
}
