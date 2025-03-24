<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaTecnicaResource\Pages;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\CompensacaoEnvolvidosRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\CompensacaoNaoEnvolvidosRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\DiscenteVisitasRelationManager;
use App\Models\Cidade;
use App\Models\Config;
use App\Models\DadosUser;
use App\Models\SubCategoria;
use App\Models\User;
use App\Models\VisitaTecnica;
use App\Traits\CalculaValorDiarias;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Exceptions\Halt;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;


class VisitaTecnicaResource extends Resource
{
    use CalculaValorDiarias;

    protected static ?string $model = VisitaTecnica::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Visitas Técnica';

    protected static ?string $navigationGroup = 'Propostas';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(null)
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Tipo de Visita')
                        ->completedIcon('heroicon-m-hand-thumb-up')
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
                                Forms\Components\ToggleButtons::make('compensacao')
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
                        ->completedIcon('heroicon-m-hand-thumb-up')
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
                                    ->required(false),
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
                        ->completedIcon('heroicon-m-hand-thumb-up')
                        ->beforeValidation(function ($state, $get, $set) {
                            if ($get('qtd_estudantes') != '') {
                                Self::calculaValorDiarias($state, $get, $set);
                            }
                        })
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
                                    ->seconds(false)
                                    // ->format('DD/MM/YYYY HH:mm')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('data_hora_retorno')
                                    ->label('Data e Hora de Retorno')
                                    ->seconds(false)
                                    //->format('d/m/y HH:mm')
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
                                    ->seconds(false)
                                    ->required(),
                            ]),

                        ]),
                    Wizard\Step::make('Custos e Estudantes')
                        ->completedIcon('heroicon-m-hand-thumb-up')
                        ->schema([
                            Grid::make([
                                'xl' => 3,
                                '2xl' => 3,
                            ])->schema([
                                Forms\Components\TextInput::make('qtd_estudantes')
                                    ->label('Quantidade de Estudantes')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $set, $state, $get) {

                                        Self::calculaValorDiarias($state, $get, $set);

                                        // // VARIAVEIS
                                        // $qtdEstudantes =  $state;
                                        // $valorMeiaDiaria = Config::all()->first()->valor_meia_diaria;

                                        // $dataHoraSaida = $get('data_hora_saida');
                                        // $dataHoraRetorno = $get('data_hora_retorno');

                                        // // CALCULA DIAS DE VIAGEM
                                        // $saida = Carbon::parse($dataHoraSaida)->format('Y-m-d');
                                        // $retorno = Carbon::parse($dataHoraRetorno)->format('Y-m-d');
                                        // $totalHoras = Carbon::parse($retorno)->diffInHours(Carbon::parse($saida)->startOfDay());
                                        // $days = floor($totalHoras / 24);

                                        // if ($days < 1) {
                                        //     $valorDiarias =  ($qtdEstudantes * $valorMeiaDiaria);
                                        // } elseif ($days >= 1 && $days < 2) {
                                        //     $valorDiarias =  ($qtdEstudantes * ($valorMeiaDiaria * 3));
                                        // } elseif ($days >= 2) {
                                        //     $valorDiarias =  ($qtdEstudantes * ((($valorMeiaDiaria * 2) * $days) + $valorMeiaDiaria));
                                        // }
                                        // // dd($qtdEstudantes, $valorMeiaDiaria, $days,' = ', $valorDiarias);
                                        // $set('valor_total_diarias', $valorDiarias);
                                        // $set('custo_total', ($valorDiarias + $get('menor_valor_hospedagem')));
                                    })
                                    ->numeric()
                                    ->required(),
                                Forms\Components\ToggleButtons::make('hospedagem')
                                    ->label('Haverá Hospedagem?')
                                    ->hidden(fn(Get $get): bool => !$get('custo'))
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        $set('menor_valor_hospedagem', 0);
                                        $set('custo_total', ($get('valor_total_diarias') + 0));
                                        $set('cotacoes_hospedagem', []);
                                    })
                                    ->required(fn(Get $get): bool => $get('custo') == true)
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\Textarea::make('justificativa_hospedagem')
                                    ->label('Justificar Hospedagem')
                                    ->hidden(fn(Get $get) => !$get('hospedagem'))
                                    ->required(fn(Get $get) => $get('hospedagem'))
                                    ->autosize()
                                    ->maxLength(255),

                                Forms\Components\Repeater::make('cotacao_hospedagem')
                                    ->columnSpan(
                                        [
                                            'xl' => 3,
                                            '2xl' => 3,
                                        ]
                                    )
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
                                                ->hidden(fn(Get $get): bool => !$get('custo'))
                                                ->hidden()
                                                ->readOnly()
                                                ->prefix('R$')
                                                ->numeric()
                                                ->required(),
                                            Forms\Components\TextInput::make('menor_valor_hospedagem')
                                                ->label('Menor Valor de Hospedagem')
                                                ->hidden(fn(Get $get) => !$get('hospedagem') || !$get('custo'))
                                                ->prefix('R$')
                                                ->readOnly()
                                                ->numeric()
                                                ->required(),
                                            Forms\Components\TextInput::make('custo_total')
                                                ->label('Custo Total da Visita')
                                                ->hidden(fn(Get $get): bool => !$get('custo'))
                                                ->prefix('R$')
                                                ->numeric()
                                                ->readOnly()
                                                ->required(),
                                        ]),
                                    ])
                            ]),
                        ]),
                    Wizard\Step::make('Justificativa e Objetivos')
                        ->completedIcon('heroicon-m-hand-thumb-up')
                        ->schema([
                            Split::make([
                                Section::make([
                                    Forms\Components\Textarea::make('conteudo_programatico')
                                        ->label('Conteúdo Programático')
                                        ->autosize()
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('justificativa')
                                        ->label('Justificativa')
                                        ->autosize()
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('objetivos')
                                        ->label('Objetivos')
                                        ->autosize()
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('motodologia')
                                        ->label('Metodologia')
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('form_avalia_aprend')
                                        ->label('Forma de Avaliação da Aprendizagem')
                                        ->autosize()
                                        ->required()
                                        ->columnSpanFull(),
                                ]),
                                Section::make([
                                    ToggleButtons::make('status')
                                        ->label('Status')
                                        ->required()
                                        ->default('0')
                                        ->inline(false)
                                        ->options([
                                            '0' => 'Submetida',
                                            '1' => 'Autorizada',
                                            '2' => 'Finalizada'
                                        ])
                                        ->colors([
                                            '0' => 'danger',
                                            '1' => 'success',
                                            '2' => 'info',
                                        ])
                                        ->icons([
                                            '0' => 'heroicon-o-pencil',
                                            '1' => 'heroicon-o-clock',
                                            '2' => 'heroicon-o-check-circle',
                                        ])
                                ])->grow(false),
                            ])->from('md')

                        ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->Label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                        '2' => 'info',
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state == 0) {
                            return 'Submetida';
                        }
                        if ($state == 1) {
                            return 'Autorizada';
                        }
                        if ($state == 3) {
                            return 'Finalizada';
                        }
                    }),
                Tables\Columns\TextColumn::make('emp_evento')
                    ->label('Empresa/Evento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria.nome')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subCategoria.nome')
                    ->label('Sub Categoria')
                    ->sortable(),
                Tables\Columns\TextColumn::make('professor.name')
                    ->label('Professor Responsável')
                    ->sortable(),
                Tables\Columns\TextColumn::make('turma.nome')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado.nome')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cidade.nome')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_hora_saida')
                    ->label('Data e Hora de Saída')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_hora_retorno')
                    ->label('Data e Hora de Retorno')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),


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
            DiscenteVisitasRelationManager::class,
            CompensacaoEnvolvidosRelationManager::class,
            CompensacaoNaoEnvolvidosRelationManager::class,
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
