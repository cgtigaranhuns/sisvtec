<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaTecnicaResource\Pages;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\CompensacaoDocenteNaoEnvolvidoRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\CompensacaoTurmaNaoEnvolvidoRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\DiscenteVisitasRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\RelatorioFinalVisitaTecnicaRelationManager;
use App\Models\Cidade;
use App\Models\SubCategoria;
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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


class VisitaTecnicaResource extends Resource
{
    use CalculaValorDiarias;

    protected static ?string $model = VisitaTecnica::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Visitas Técnica';

    protected static ?string $navigationGroup = 'Propostas';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User */
        $authUser = auth()->user();

        if ($authUser->hasRole('Coordenadores')) {
            // dd('teste');
            return static::getModel()::query()->where('coordenacao_id', '=', auth()->user()->coordenacao_id);
        }
        if ($authUser->hasRole('Professores')) {
            return static::getModel()::query()->where('professor_id', '=', auth()->user()->id);
        }
        // Add a valid condition or remove this block if not needed
        else {
            return static::getModel()::query(); // Default return for other cases
        }
    }


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
                                    ->default(2)
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->relationship('categoria', 'nome')
                                    ->live()
                                    ->required(),

                                Forms\Components\Select::make('sub_categoria_id')
                                    ->label('Sub Categoria')
                                    ->default(1)
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->live()
                                    ->options(fn(Get $get): Collection => SubCategoria::query()
                                        ->where('categoria_id', $get('categoria_id'))
                                        ->pluck('nome', 'id'))
                                    ->required(),
                                Forms\Components\ToggleButtons::make('custo')
                                    ->label('Haverá Custo?')
                                    ->required()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\ToggleButtons::make('compensacao')
                                    ->label('Haverá Compensação?')
                                    ->required()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\Textarea::make('emp_evento')
                                    ->columnSpan([
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ])
                                    ->label('Empresa ou Evento')
                                    ->autosize()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
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
                                    ->label('Coordenação/Setor')
                                    ->searchable()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->relationship('coordenacao', 'nome')
                                    ->required(),
                                Forms\Components\Select::make('curso_id')
                                    ->label('Curso')
                                    ->searchable()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->relationship('curso', 'nome')
                                    ->required(),
                                Forms\Components\Select::make('turma_id')
                                    ->label('Turma')
                                    ->searchable()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->relationship('turma', 'nome')
                                    ->multiple()
                                    ->required(),
                                Forms\Components\Select::make('disciplina_id')
                                    ->label('Componentes Curriculares')
                                    ->relationship('disciplina', 'nome')
                                    ->searchable()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->live()
                                    ->multiple()
                                    ->required(fn(Get $get) => $get('categoria_id') != 1),
                                Forms\Components\Select::make('professor_id')
                                    ->label('Professor Responsável')
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->default(function () {
                                        return  auth()->user()->id;
                                    })
                                    ->searchable()
                                    ->relationship('professor', 'name')
                                    ->required(),
                                Forms\Components\Select::make('srv_participante_id')
                                    ->label('Servidores Participantes')
                                    ->relationship('srvParticipante', 'name')
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->username} - {$record->name}" . ($record->cargo ? " - {$record->cargo->nome}" : ""))
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
                                                ->disabled(function ($context, Get  $get) {
                                                    if (($get('status') != 0) && $context == 'edit') {
                                                        return true;
                                                    } else {
                                                        return false;
                                                    }
                                                })
                                                ->autosize(),                                                
                                            Forms\Components\Textarea::make('just_outra_disciplina')
                                                ->label('Justificar Outras Disciplinas')
                                                ->hidden(fn(Get $get) => count($get('disciplina_id') ?? []) <= 1)
                                                ->required(fn(Get $get) => count($get('disciplina_id') ?? []) > 1)
                                                ->disabled(function ($context, Get  $get) {
                                                    if (($get('status') != 0) && $context == 'edit') {
                                                        return true;
                                                    } else {
                                                        return false;
                                                    }
                                                })
                                                ->autosize(),
                                                
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
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->label('Estado')
                                    ->live()
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('cidade_id')
                                    ->options(fn(Get $get): Collection => Cidade::query()
                                        ->where('estado_id', $get('estado_id'))
                                        ->pluck('nome', 'id'))
                                    ->label('Cidade')
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->searchable()
                                    ->required(),
                                Forms\Components\DateTimePicker::make('data_hora_saida')
                                    ->label('Data e Hora de Saída')
                                    ->seconds(false)
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->required(),
                                Forms\Components\DateTimePicker::make('data_hora_retorno')
                                    ->label('Data e Hora de Retorno')
                                    ->seconds(false)
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
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
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->label('Carga Horária Total da Viagem')
                                    ->required(),
                                Forms\Components\TextInput::make('carga_horaria_visita')
                                    ->label('Carga Horária Total da Visita')
                                    ->mask('99:99')
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->required(fn(Get $get) => $get('categoria_id') != 1),
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
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->afterStateUpdated(function (callable $set, $state, $get) {

                                        Self::calculaValorDiarias($state, $get, $set);
                                    })
                                    ->numeric()
                                    ->required(),
                                Forms\Components\ToggleButtons::make('hospedagem')
                                    ->label('Haverá Hospedagem?')
                                    ->hidden(fn(Get $get): bool => $get('custo') == false)
                                    ->live()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
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
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
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
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
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
                                            ->disabled(function ($context, Get  $get) {
                                                if (($get('status') != 0) && $context == 'edit') {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            })
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('valor2')
                                            ->label('Valor 2')
                                            ->prefix('R$')
                                            ->disabled(function ($context, Get  $get) {
                                                if (($get('status') != 0) && $context == 'edit') {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            })
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\TextInput::make('valor3')
                                            ->label('Valor 3')
                                            ->prefix('R$')
                                            ->disabled(function ($context, Get  $get) {
                                                if (($get('status') != 0) && $context == 'edit') {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            })
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
                                                ->hidden(fn(Get $get): bool => $get('custo') == false)
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
                                        ->label('Conteúdo Programático/Resumo')
                                        ->autosize()
                                        ->disabled(function ($context, Get  $get) {
                                            if (($get('status') != 0) && $context == 'edit') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        })
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('justificativa')
                                        ->label('Justificativa')
                                        ->autosize()
                                        ->required()
                                        ->disabled(function ($context, Get  $get) {
                                            if (($get('status') != 0) && $context == 'edit') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        })
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('objetivos')
                                        ->label('Objetivos')
                                        ->autosize()
                                        ->required()
                                        ->disabled(function ($context, Get  $get) {
                                            if (($get('status') != 0) && $context == 'edit') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        })
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('metodologia')
                                        ->label('Metodologia')
                                        ->autosize()
                                        ->disabled(function ($context, Get  $get) {
                                            if (($get('status') != 0) && $context == 'edit') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        })
                                        ->required(fn(Get $get): bool => $get('categoria_id') != 1)
                                        ->required()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('form_avalia_aprend')
                                        ->label('Forma de Avaliação da Aprendizagem')
                                        ->autosize()
                                        ->required(fn(Get $get): bool => $get('categoria_id') != 1)
                                        ->columnSpanFull(),
                                ]),
                                Section::make([
                                    ToggleButtons::make('status')
                                        ->label('Status')
                                        ->required()
                                        ->hidden(function ($context, Get  $get) {

                                            /** @var \App\Models\User */
                                            $authUser =  auth()->user();
                                            // dd($authUser->hasRole('Professor'));
                                            if ($authUser->hasRole('Professores')) {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        })
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
                Tables\Columns\TextColumn::make('emp_evento')
                    ->label('Empresa/Evento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria.nome')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subCategoria.nome')
                    ->label('Sub Categoria')
                    ->sortable(),
                Tables\Columns\TextColumn::make('coordenacao.nome')
                    ->label('Coordenação')
                    ->sortable(),
                Tables\Columns\TextColumn::make('professor.name')
                    ->label('Professor Responsável')
                    ->sortable(),
                // Tables\Columns\TextColumn::make('turma.nome')
                //     ->label('Turma')
                //     ->sortable()
                //     ->searchable(),

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
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        '0' => 'Submetida',
                        '1' => 'Autorizada',
                        '2' => 'Finalizada',
                    ])
                    ->alignCenter()
                    ->sortable()
                    ->disabled(function () {

                        /** @var \App\Models\User */
                        $authUser =  auth()->user();
                        //  dd($authUser->getRoleNames()->first());
                        if ($authUser->hasRole('Professores')) {
                            return true;
                        } else {
                            return false;
                        }
                    }),


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
                Tables\Actions\Action::make('imprimirVisitaTecnica')
                    ->icon('heroicon-o-printer')
                    ->disabled(function (VisitaTecnica $record) {
                        /** @var \App\Models\User */
                        $authUser =  auth()->user();
                        if ($authUser->hasRole('Professores') && $record->status == 0) {
                            return true;
                        } else {
                            return false;
                        }
                    })

                    ->label('Visita Técnica')
                    ->url(fn(VisitaTecnica $record): string => route('imprimirVisitaTecnica', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('imprimirAta')
                    ->icon('heroicon-o-printer')
                    ->disabled(fn(VisitaTecnica $record): bool => $record->status == 0)
                    ->label('Ata da Visita Técnica')
                    ->url(fn(VisitaTecnica $record): string => route('imprimirAtaVisitaTecnica', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('imprimirRelatorioFinal')
                    ->icon('heroicon-o-printer')
                    ->disabled(fn(VisitaTecnica $record): bool => $record->status == 0)
                    ->label('Relatório Final')
                    ->url(fn(VisitaTecnica $record): string => route('imprimirRelatorioFinal', $record))
                    ->openUrlInNewTab(),


                Tables\Actions\EditAction::make()
                    ->before(function ($record, $data) {
                        Notification::make()
                            ->title('Proposta criada com sucesso!')
                            ->body('Agora é necessário adicionar os dicentes que irão para Visita Técnica. Clique no botão "Adicionar Discente" e preencha os planos de compensação, caso necessário.')
                            ->success()
                            ->persistent() // Uncommented to make the notification persistent
                            ->send();
                    }),
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
            CompensacaoDocenteNaoEnvolvidoRelationManager::class,
            CompensacaoTurmaNaoEnvolvidoRelationManager::class,
            RelatorioFinalVisitaTecnicaRelationManager::class,


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
