<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaTecnicaResource\Pages;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers;
use App\Models\Cidade;
use App\Models\Config;
use App\Models\DadosUser;
use App\Models\SubCategoria;
use App\Models\User;
use App\Models\VisitaTecnica;
use Carbon\Carbon;
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
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->username} - {$record->name} - {$record->cargo->nome}")
                                    ->searchable(['username', 'name'])
                                    ->multiple()
                                    ->required(),
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
                            Grid::make([
                                'xl' => 3,
                                '2xl' => 3,
                            ])->schema([
                                Forms\Components\Select::make('estado_id')
                                    ->relationship('estado', 'nome')
                                    ->label('Estado')
                                    ->live()
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('cidade_id')
                                    ->options(fn(Get $get): Collection => Cidade::query()
                                        ->where('estado_id', $get('estado_id'))
                                        ->pluck('nome', 'id'))
                                    ->label('Cidade')
                                    ->searchable()
                                    ->required(),
                                Forms\Components\DateTimePicker::make('data_hora_saida')
                                    ->label('Data e Hora de Saída')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('data_hora_retorno')
                                    ->label('Data e Hora de Retorno')
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        $dataHoraSaida = $get('data_hora_saida');
                                        $dataHoraRetorno = $get('data_hora_retorno');

                                        if ($dataHoraSaida && $dataHoraRetorno) {
                                            $saida = \Carbon\Carbon::parse($dataHoraSaida);
                                            $retorno = \Carbon\Carbon::parse($dataHoraRetorno);
                                            $totalHoras = $retorno->diffInHours($saida);
                                            $days = floor($totalHoras / 24);
                                            $hours = $totalHoras % 24;
                                            $minutes = $retorno->diffInMinutes($saida) % 60;
                                            $humanReadable = "{$days} dias {$hours} horas e {$minutes} minutos";
                                            $set('carga_horaria_total', $humanReadable);
                                        }
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('carga_horaria_total')
                                    ->readOnly()
                                    ->label('Carga Horária Total da Viagem')
                                    ->required(),
                                Forms\Components\TimePicker::make('carga_horaria_visita')
                                    ->label('Carga Horária Total da Visita')
                                    ->required(),
                            ]),

                        ]),
                    Wizard\Step::make('Custos e Estudantes')
                        ->schema([
                            Grid::make([
                                'xl' => 3,
                                '2xl' => 3,
                            ])->schema([
                                Forms\Components\TextInput::make('qtd_estudantes')
                                    ->label('Quantidade de Estudantes')
                                    ->live(onBlur:true)
                                    ->afterStateUpdated(function (callable $set, $state, $get) {

                                        // VARIAVEIS
                                        $qtdEstudantes =  $state;
                                        $valorMeiaDiaria = Config::all()->first()->valor_meia_diaria;

                                        $dataHoraSaida = $get('data_hora_saida');
                                        $dataHoraRetorno = $get('data_hora_retorno');                                        
                                        
                                        // CALCULA DIAS DE VIAGEM
                                        $saida = Carbon::parse($dataHoraSaida)->format('Y-m-d');
                                        $retorno = Carbon::parse($dataHoraRetorno)->format('Y-m-d');
                                        $totalHoras = Carbon::parse($retorno)->diffInHours(Carbon::parse($saida)->startOfDay());
                                        $days = floor($totalHoras / 24);
                                        
                                        if($days < 1){
                                            $valorDiarias =  $qtdEstudantes * $valorMeiaDiaria;
                                        }
                                        elseif($days >= 1){
                                            $valorDiarias =  $qtdEstudantes * ($valorMeiaDiaria * 3);
                                        }
                                        elseif($days >= 2){
                                            $valorDiarias =  $qtdEstudantes * (($valorMeiaDiaria * 3) * $days);
                                        }
                                      //  dd($valorDiarias);
                                        $set('valor_total_diarias', $valorDiarias);
                                        $set('custo_total', ($valorDiarias + $get('menor_valor_hospedagem')));
                                       
                                       

                                          
                                        
                                    })
                                    ->numeric()
                                    ->required(),
                                Forms\Components\ToggleButtons::make('hospedagem')
                                    ->label('Haverá Hospedagem?')
                                    ->live()
                                    ->required()
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\Textarea::make('justificativa_hospedagem')
                                    ->label('Justificar Hospedagem')
                                    ->hidden(fn(Get $get) => !$get('hospedagem'))
                                    ->required(fn(Get $get) => $get('hospedagem'))
                                    ->autosize()
                                    ->maxLength(255),

                                Forms\Components\Repeater::make('cotacoes_hospedagem')
                                    ->columnSpan(3)
                                    ->live(onBlur: true)
                                    ->minItems(1)
                                    ->maxItems(1)
                                    ->columns(3)
                                    ->label('Cotações de Hospedagem')
                                    ->hidden(fn(Get $get) => !$get('hospedagem'))
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $minValue = min(array_map(function ($item) {
                                            return min($item['valor1'] ?? PHP_INT_MAX, $item['valor2'] ?? PHP_INT_MAX, $item['valor3'] ?? PHP_INT_MAX);
                                        }, $state));

                                        $set('menor_valor_hospedagem', $minValue);
                                        $set('custo_total', ($get('valor_total_diarias') + $minValue));
                                    })
                                    ->schema([
                                        Forms\Components\TextInput::make('valor1')
                                            ->label('Valor 1')
                                            ->prefix('R$')
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('valor2')
                                            ->label('Valor 2')
                                            ->prefix('R$')
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('valor3')
                                            ->label('Valor 3')
                                            ->prefix('R$')
                                            ->required()
                                            ->numeric(),
                                    ]),
                                    Forms\Components\Fieldset::make('Custos')
                                        ->schema([
                                            Grid::make([
                                                'xl' => 3,
                                                '2xl' => 3,
                                            ])->schema([
                                                Forms\Components\TextInput::make('valor_total_diarias')
                                                    ->label('Valor Total das Diárias')
                                                    ->readOnly()
                                                    ->prefix('R$')
                                                    ->numeric()
                                                    ->required(),
                                                Forms\Components\TextInput::make('menor_valor_hospedagem')
                                                    ->label('Menor Valor de Hospedagem')
                                                    ->hidden(fn(Get $get) => !$get('hospedagem'))
                                                    ->prefix('R$')
                                                    ->readOnly()
                                                    ->numeric()
                                                    ->required(),
                                                Forms\Components\TextInput::make('custo_total')
                                                    ->label('Custo Total da Visita')
                                                    ->prefix('R$')
                                                    ->numeric()
                                                    ->readOnly()
                                                    ->required(),
                                            ]),
                                        ])                                
                            ]),
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
