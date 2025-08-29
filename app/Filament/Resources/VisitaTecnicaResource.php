<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitaTecnicaResource\Pages;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\CompensacaoDocenteNaoEnvolvidoRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\CompensacaoTurmaNaoEnvolvidoRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\DiscenteVisitasRelationManager;
use App\Filament\Resources\VisitaTecnicaResource\RelationManagers\RelatorioFinalVisitaTecnicaRelationManager;
use App\Mail\PropostaStatusEmail;
use App\Mail\TermoCompromisso;
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
use Illuminate\Support\Facades\Mail;
use App\Http\ControlerImpressoes;
use App\Http\Controllers\ControllerImpressoes;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class VisitaTecnicaResource extends Resource
{
    use CalculaValorDiarias;

    protected static ?string $model = VisitaTecnica::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Atividade Extraclasse';

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
                    Wizard\Step::make('Tipo de Atividade')
                        ->completedIcon('heroicon-m-hand-thumb-up')
                        ->schema([
                            Grid::make([
                                'xl' => 2,
                                '2xl' => 2,
                            ])->schema([
                                Forms\Components\Select::make('categoria_id')
                                    ->label('Categoria')
                                    // ->default(function ($context) {
                                    //     return $context === 'create' ? 2 : null;
                                    // })
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
                                    // ->default(function ($context) {
                                    //     return $context === 'create' ? 1 : null;
                                    // })
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
                                    ->label('Lugares e/ou Eventos a ser Visitados')
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
                                    ->default(function () {
                                        return  auth()->user()->coordenacao_id;
                                    })
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
                                    ->relationship('curso', 'nome')
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->multiple()
                                    ->required(),
                                Forms\Components\Select::make('turma_id')
                                    ->label('Turma')
                                    ->relationship('turma', 'nome')
                                    ->searchable()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
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
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Get $get) {
                                        $saida = \Carbon\Carbon::parse($state);
                                        //  dd($get('custo') == true && $saida->lt(Carbon::now()->addDays(30)));
                                        if ($get('custo') == true && $saida->lt(Carbon::now()->addDays(30))) {
                                            Notification::make()
                                                ->title('ATENÇÃO!')
                                                ->body('Considerando que é uma proposta que envolverá custos, a data de saída não deve ser inferior a 30 dias.')
                                                ->warning()
                                                ->duration(10000)
                                                ->send();
                                        }
                                        if ($get('custo') == false && $saida->lt(Carbon::now()->addDays(7))) {
                                            Notification::make()
                                                ->title('ATENÇÃO!')
                                                ->body('Embora sejá uma proposta que não envolverá custos, a data de saída não deve ser inferior a 7 dias. Pois, pode 
                                                não ser possível agendar o transporte.')
                                                ->warning()
                                                ->duration(10000)
                                                ->send();
                                        }
                                    })
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
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        $dataHoraSaida = $get('data_hora_saida');
                                        $dataHoraRetorno = $get('data_hora_retorno');

                                        $dataHoraSaida = $get('data_hora_saida');
                                        $dataHoraRetorno = $state;

                                        if ($dataHoraSaida && $dataHoraRetorno) {
                                            $saida = \Carbon\Carbon::parse($dataHoraSaida);
                                            $retorno = \Carbon\Carbon::parse($dataHoraRetorno);

                                            if ($saida->gt($retorno)) {
                                                $set('data_hora_retorno', null);
                                                Notification::make()
                                                    ->title('ATENÇÃO!')
                                                    ->body('A data/hora de retorno não pode ser anterior à data/hora de saída.')
                                                    ->danger()
                                                    ->send();
                                            }

                                            if ($saida->gt($retorno)) {
                                                $set('data_hora_retorno', null);
                                                Notification::make()
                                                    ->title('ATENÇÃO!')
                                                    ->body('A data/hora de retorno não pode ser anterior à data/hora de saída.')
                                                    ->danger()
                                                    ->send();
                                            }
                                            // dd(!$saida->lt(Carbon::now()->addDays(30)));


                                        }

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
                                    ->label('Carga Horária Total da Atividade')
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

                                    ##### Hospedagem #####

                                Forms\Components\ToggleButtons::make('hospedagem')
                                    ->label('Haverá Hospedagem?')
                                    ->hidden(fn(Get $get): bool => $get('custo') == false)
                                    ->default(function (Get $get) {
                                        $dataSaida = Carbon::parse($get('data_hora_saida'))->startOfDay();
                                        $dataRetorno = Carbon::parse($get('data_hora_retorno'))->startOfDay();
                                        if ($dataSaida->equalTo($dataRetorno)) {
                                            return false;
                                        } else {
                                            return true;
                                        }
                                    })
                                    ->live()
                                    // ->disableOptionWhen(fn (string $value, $get): bool => $value == true ||Carbon::parse($get('data_hora_saida'))->startOfDay()->equalTo(Carbon::parse($get('data_hora_retorno'))->startOfDay()))
                                    ->disableOptionWhen(
                                        function (string $value, $get) {
                                            $dataSaida = Carbon::parse($get('data_hora_saida'))->startOfDay();
                                            $dataRetorno = Carbon::parse($get('data_hora_retorno'))->startOfDay();
                                            if ($dataSaida->equalTo($dataRetorno)) {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        }

                                    )
                                    ->disabled(function ($context, Get  $get) {
                                        $dataSaida = Carbon::parse($get('data_hora_saida'))->startOfDay();
                                        $dataRetorno = Carbon::parse($get('data_hora_retorno'))->startOfDay();

                                        //  dd($dataSaida->equalTo($dataRetorno));
                                        if ((($get('status') != 0) && $context == 'edit') /* or  $dataSaida->equalTo($dataRetorno) */) {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        $set('menor_valor_hospedagem', 0);
                                        $set('custo_total', ($get('valor_total_diarias') + $get('menor_valor_passagens') + $get('valor_inscricao') + 0));
                                        $set('cotacao_hospedagem', []);
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
                                        $set('custo_total', ($get('valor_total_diarias') + $get('menor_valor_passagens') + $get('valor_inscricao') + $minValue));
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

                                    ######### Passagens #########

                                Forms\Components\ToggleButtons::make('passagens')
                                    ->label('Haverá Passagens?')
                                    ->hidden(fn(Get $get): bool => $get('custo') == false)
                                    ->default(false)
                                    ->live()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        $set('menor_valor_passagens', 0);
                                        $set('custo_total', ($get('valor_total_diarias') + $get('menor_valor_hospedagem') + $get('valor_inscricao') + 0));
                                        $set('cotacao_passagens', []);
                                    })
                                    ->required(fn(Get $get): bool => $get('custo') == true)
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\Textarea::make('justificativa_passagens')
                                    ->label('Justificar Passagens')
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->hidden(fn(Get $get) => !$get('passagens'))
                                    ->required(fn(Get $get) => $get('passagens'))
                                    ->autosize()
                                    ->maxLength(255),

                                Forms\Components\Repeater::make('cotacao_passagens')
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
                                    ->label('Cotações de Passagens')
                                    ->hidden(fn(Get $get) => !$get('passagens'))
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $minValue = min(array_map(function ($item) {
                                            return min($item['valor1_passagens'] ?? PHP_INT_MAX, $item['valor2_passagens'] ?? PHP_INT_MAX, $item['valor3_passagens'] ?? PHP_INT_MAX);
                                        }, $state));

                                        $set('menor_valor_passagens', $minValue);
                                        $set('custo_total', ($get('valor_total_diarias') + $get('menor_valor_hospedagem') + $get('valor_inscricao') + $minValue));
                                    })
                                    ->schema([
                                        Forms\Components\TextInput::make('valor1_passagens')
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
                                        Forms\Components\TextInput::make('valor2_passagens')
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
                                        Forms\Components\TextInput::make('valor3_passagens')
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

                                        ##### Inscrição #####
                                Forms\Components\ToggleButtons::make('inscricao')
                                    ->label('Haverá Inscrição?')
                                    ->hidden(fn(Get $get): bool => $get('custo') == false)
                                    ->default(false)
                                    ->live()
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        if (!$state) {
                                            $set('valor_inscricao', 0);
                                        }
                                        $set('custo_total', ($get('valor_total_diarias') + $get('menor_valor_hospedagem') + $get('menor_valor_passagens') + ($state ? $get('valor_inscricao') : 0)));
                                    })
                                    ->required(fn(Get $get): bool => $get('custo') == true)
                                    ->boolean()
                                    ->grouped(),
                                Forms\Components\Textarea::make('justificativa_inscricao')
                                    ->label('Justificar Inscrição')
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->hidden(fn(Get $get) => !$get('inscricao'))
                                    ->required(fn(Get $get) => $get('inscricao'))
                                    ->autosize()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('valor_inscricao')
                                    ->label('Valor Total das Inscrições')
                                    ->prefix('R$')
                                    ->live(onBlur: true)
                                    ->disabled(function ($context, Get  $get) {
                                        if (($get('status') != 0) && $context == 'edit') {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    })
                                    ->hidden(fn(Get $get) => !$get('inscricao'))
                                    ->afterStateUpdated(function (callable $set, $state, $get) {
                                        $set('custo_total', ($get('valor_total_diarias') + $get('menor_valor_hospedagem') + $get('menor_valor_passagens') + ($get('inscricao') ? $state : 0)));
                                    })
                                    ->numeric()
                                    ->required(fn(Get $get) => $get('inscricao')),  


                                Forms\Components\Fieldset::make('Custos - Apenas informativo (Diarias + Hospedagens + Passagens + Inscrições)')
                                    ->schema([
                                        Grid::make([
                                            'xl' => 4,
                                            '2xl' => 4,
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
                                            Forms\Components\TextInput::make('menor_valor_passagens')
                                                ->label('Menor Valor de Passagens')
                                                ->hidden(fn(Get $get) => !$get('passagens') || !$get('custo'))  
                                                ->prefix('R$')
                                                ->readOnly()
                                                ->numeric()
                                                ->required(),
                                            Forms\Components\TextInput::make('custo_total')
                                                ->label('Total Geral')                                                
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
                                        ->label('Conteúdo Programático/Resumo por Disciplina')
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
                                        ->label('Justificativa Geral')
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
                                        ->label('Objetivos por Disciplina')
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
                                        ->label('Metodologia por Disciplina')
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
                                        ->label('Forma de Avaliação da Aprendizagem por Disciplina')
                                        ->autosize()
                                        ->disabled(function ($context, Get  $get) {
                                            if (($get('status') != 0) && $context == 'edit') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        })
                                        ->required(fn(Get $get): bool => $get('categoria_id') != 1)
                                        ->columnSpanFull(),
                                ]),
                                // Section::make([
                                //     ToggleButtons::make('status')
                                //         ->label('Status')
                                //         ->disabled(function ($context, Get  $get) {
                                //             if (($get('status') != 0) && $context == 'edit') {
                                //                 return true;
                                //             } else {
                                //                 return false;
                                //             }
                                //         })
                                //         ->required()
                                //         ->hidden(function ($context, Get  $get) {

                                //             /** @var \App\Models\User */
                                //             $authUser =  auth()->user();
                                //             // dd($authUser->hasRole('Professor'));
                                //             if ($authUser->hasRole('Professores')) {
                                //                 return true;
                                //             } else {
                                //                 return false;
                                //             }
                                //         })
                                //         ->default('0')
                                //         ->inline(false)
                                //         ->options([
                                //             '0' => 'Cadastrada',
                                //             '1' => 'Submetida',
                                //             '2' => 'Aprovada',
                                //             '3' => 'Reprovada',
                                //             '4' => 'Financeiro',
                                //             '5' => 'Finalizada',
                                //         ])
                                //         ->colors([
                                //             '0' => 'warning',
                                //             '1' => 'info',
                                //             '2' => 'success',
                                //             '3' => 'danger',
                                //             '4' => 'warning',
                                //             '5' => 'success',
                                //         ])
                                //         ->icons([
                                //             '0' => 'heroicon-o-pencil',
                                //             '1' => 'heroicon-o-clock',
                                //             '2' => 'heroicon-o-check-circle',
                                //             '3' => 'heroicon-o-x-circle',
                                //             '4' => 'heroicon-o-currency-dollar',
                                //             '5' => 'heroicon-o-check-circle',
                                //         ])
                                // ])->grow(false),
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
                    ->label('Local')
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
                        '0' => 'Cadastrada',
                        '1' => 'Submetida',
                        '2' => 'Aprovada',
                        '3' => 'Reprovada',
                        '4' => 'Financeiro',
                        '5' => 'Finalizada',
                    ])
                    ->alignCenter()
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        Mail::to($record->professor->email)->cc($record->coordenacao->email)->send(new PropostaStatusEmail($record));
                        if ($state == 2) {
                            foreach ($record->discenteVisitas as $discente) {
                                // dd($discente);
                                (new ControllerImpressoes())->imprimirTermoCompromisso($record->id);
                                Mail::to($discente->discente->email)->send(new TermoCompromisso($record, $discente->discente->id));
                            }
                            Notification::make()
                                ->title('Termos de compromisso enviados por email para os estudantes!')
                                ->success()
                                ->send();
                        }
                    })
                    ->disabled(function ($state) {

                        /** @var \App\Models\User */
                        $authUser =  auth()->user();
                        //  dd($authUser->getRoleNames()->first());
                        if ($authUser->hasRole('Professores') && $state > 0) {
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
                Filter::make('custos')
                    ->label('Com Custos')
                    ->query(fn(Builder $query): Builder => $query->where('custo', true)),
                Filter::make('sem_custos')
                    ->label('Sem Custos')
                    ->query(fn(Builder $query): Builder => $query->where('custo', false)),
                Filter::make('hospedagem')
                    ->label('Com Hóspede')
                    ->query(fn(Builder $query): Builder => $query->where('hospedagem', true)),
                Filter::make('sem_hospedagem')
                    ->label('Sem Hóspede')
                    ->query(fn(Builder $query): Builder => $query->where('hospedagem', false)),


                SelectFilter::class::make('status')
                    ->label('Status')
                    ->options([
                        '0' => 'Cadastrada',
                        '1' => 'Submetida',
                        '2' => 'Aprovada',
                        '3' => 'Reprovada',
                        '4' => 'Financeiro',
                        '5' => 'Finalizada',
                    ])
                    ->multiple()
                    ->preload(),
                SelectFilter::make('categoria_id')
                    ->label('Categoria')
                    ->relationship('categoria', 'nome')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('sub_categoria_id')
                    ->label('Sub Categoria')
                    ->relationship('subCategoria', 'nome')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('coordenacao_id')
                    ->label('Coordenação')
                    ->relationship('coordenacao', 'nome')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('professor_id')
                    ->label('Professor Responsável')
                    ->relationship('professor', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('turma_id')
                    ->label('Turma')
                    ->relationship('turma', 'nome')
                    ->multiple()
                    ->preload(),
                Tables\Filters\Filter::make('datas')
                    ->form([
                        DatePicker::make('data_saida_de')
                            ->label('Saída de:'),
                        DatePicker::make('data_retorno_ate')
                            ->label('Retorno até:'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['data_saida_de'],
                                fn($query) => $query->whereDate('data_hora_saida', '>=', $data['data_saida_de'])
                            )
                            ->when(
                                $data['data_retorno_ate'],
                                fn($query) => $query->whereDate('data_hora_retorno', '<=', $data['data_retorno_ate'])
                            );
                    })
                ], layout: FiltersLayout::Modal)->filtersFormColumns(3)

            ->actions([
                Tables\Actions\Action::make('imprimirVisitaTecnica')
                    ->icon('heroicon-o-printer')
                    ->disabled(function (VisitaTecnica $record) {
                        /** @var \App\Models\User */
                        $authUser =  auth()->user();
                        if ($authUser->hasRole('Professores') && ($record->status == 0 || $record->status == 1 || $record->status == 3)) {                            
                            return true;
                        } else {
                            return false;
                        }
                    })

                    ->label('Projeto')
                    ->url(fn(VisitaTecnica $record): string => route('imprimirVisitaTecnica', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('imprimirAta')
                    ->icon('heroicon-o-printer')
                    ->disabled(function (VisitaTecnica $record) {
                        /** @var \App\Models\User */
                        $authUser =  auth()->user();
                        if ($authUser->hasRole('Professores') && ($record->status == 0 || $record->status == 1 || $record->status == 3)) {                            
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->label('Ata de Presença')
                    ->url(fn(VisitaTecnica $record): string => route('imprimirAtaVisitaTecnica', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('imprimirRelatorioFinal')
                    ->icon('heroicon-o-printer')
                    ->disabled(fn(VisitaTecnica $record): bool => $record->status < 4)
                    ->label('Relatório Final')
                    ->url(fn(VisitaTecnica $record): string => route('imprimirRelatorioFinal', $record))
                    ->openUrlInNewTab(),


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
