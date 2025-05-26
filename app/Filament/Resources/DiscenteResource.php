<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscenteResource\Pages;
use App\Filament\Resources\DiscenteResource\RelationManagers;
use App\Models\Cidade;
use App\Models\Discente;
use App\Traits\UpdateStatusDiscentes;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Split;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt\Nop;

class DiscenteResource extends Resource
{
    protected static ?string $model = Discente::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

    protected static ?string $navigationGroup = 'Cadastros';

    protected static ?int $navigationSort = 7;

    use UpdateStatusDiscentes;

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User */
        $authUser =  auth()->user();

        if ($authUser->hasRole('Estudantes')) {
            return parent::getEloquentQuery()->where('matricula', '=', auth()->user()->username);
        } else {
            return static::getModel()::query();
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(false)
            ->schema([
                Split::make([
                    Forms\Components\Section::make('Informações Pessoais')
                        ->columns([
                            'xl' => 2,
                            '2xl' => 2,
                        ])
                        ->schema([
                            Forms\Components\TextInput::make('nome')
                                ->columnSpanFull()
                                ->readOnly(function () {

                                    /** @var \App\Models\User */
                                    $authUser =  auth()->user();
                                    if ($authUser->hasRole('Estudantes')) {
                                        return true;
                                    }
                                })
                                ->required()
                                ->maxLength(50),
                            Forms\Components\TextInput::make('nome_social')
                                ->label('Nome Social')
                                ->columnSpanFull()
                                ->maxLength(50),
                            Forms\Components\TextInput::make('matricula')
                                ->label('Matrícula')
                                ->readOnly(function () {

                                    /** @var \App\Models\User */
                                    $authUser =  auth()->user();
                                    if ($authUser->hasRole('Estudantes')) {
                                        return true;
                                    }
                                })
                                ->required()
                                ->maxLength(50),
                            Forms\Components\TextInput::make('contato')
                                ->label('Contato')
                                ->mask('(99) 99999-9999')
                                ->required()
                                ->maxLength(15),
                            Forms\Components\Textarea::make('endereco')
                                ->label('Endereço')
                                ->columnSpanFull(),


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
                            Forms\Components\TextInput::make('cep')
                                ->label('CEP')
                                ->mask('99999-999'),

                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(50),
                            Forms\Components\DatePicker::make('data_nascimento')
                                ->label('Data de Nascimento')
                                ->required(),
                            Forms\Components\Select::make('curso_id')
                                ->label('Curso')
                                ->relationship('curso', 'nome')
                                ->required(),
                            Forms\Components\Select::make('turma_id')
                                ->label('Turma')
                                ->relationship('turma', 'nome')
                                ->required(),
                            FileUpload::make('foto')
                                ->alignCenter()
                                ->directory('fotos_discentes')
                                ->image()
                                ->avatar()
                                ->imageEditor()
                                ->circleCropper()
                                ->downloadable()
                                ->maxSize(1024),
                            Forms\Components\Fieldset::make('Documentos e Banco')
                                ->schema([
                                    Forms\Components\TextInput::make('cpf')
                                        ->label('CPF')
                                        ->mask('999.999.999-99')
                                        ->rule('cpf')
                                        ->required()
                                        ->maxLength(14),
                                    Forms\Components\TextInput::make('rg')
                                        ->label('RG')
                                        ->required()
                                        ->maxLength(10),
                                    Forms\Components\TextInput::make('orgao_exp_rg')
                                        ->label('Orgão Expedidor')
                                        ->required()
                                        ->maxLength(10),
                                    Forms\Components\DatePicker::make('data_exp_rg')
                                        ->date('d/m/Y')
                                        ->label('Data de Expedição')
                                        ->required(),
                                    Forms\Components\Select::make('banco_id')
                                        ->label('Banco')
                                        ->relationship('banco', 'nome')
                                        ->required(),
                                    Forms\Components\TextInput::make('agencia')
                                        ->label('Agência')
                                        ->required()
                                        ->maxLength(8),
                                    Forms\Components\TextInput::make('conta')
                                        ->label('Conta')
                                        ->required()
                                        ->maxLength(10),
                                    Forms\Components\Radio::make('tipo_conta')
                                        ->options([
                                            '1' => 'Conta Corrente',
                                            '2' => 'Conta Poupança',
                                        ])
                                        ->label('Tipo de Conta')
                                        ->required(),
                                ]),
                        ]),

                    Forms\Components\Section::make('Situação')

                        ->schema([
                            Forms\Components\ToggleButtons::make('status')
                                ->label('Status')
                                ->required()
                                ->disabled(function () {

                                    /** @var \App\Models\User */
                                    $authUser =  auth()->user();
                                    if ($authUser->hasRole('Estudantes')) {
                                        return true;
                                    }
                                })
                                ->default('3')
                                ->options([
                                    '0' => 'Pendência Financeira',
                                    '1' => 'Cadastro Incompleto',
                                    '2' => 'Desativado',
                                    '3' => 'OK',
                                ])
                                ->colors([
                                    '0' => 'danger',
                                    '1' => 'warning',
                                    '2' => 'info',
                                    '3' => 'success',
                                ])
                                ->icons([
                                    '0' => 'heroicon-o-currency-dollar',
                                    '1' => 'heroicon-o-document-text',
                                    '2' => 'heroicon-o-no-symbol',
                                    '3' => 'heroicon-o-check',
                                ]),
                        ])->grow(false),
                ])->from('md'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('turma.nome')
            ->defaultSort('nome', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('matricula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('curso.nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('turma.nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->Label('Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'warning',
                        '2' => 'info',
                        '3' => 'success',
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state == 0) {
                            return 'Pendência Financeira';
                        }
                        if ($state == 1) {
                            return 'Cadastro Incompleto';
                        }
                        if ($state == 2) {
                            return 'Desativado';
                        }
                        if ($state == 3) {
                            return 'OK';
                        }
                    }),
                Tables\Columns\TextColumn::make('status_qa')
                    ->label('Status Q-Academico')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => $state === 'Matriculado' ? 'success' : 'danger'),

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

                Tables\Actions\EditAction::make()
                    ->after(function (Model $record, array $data) {
                        self::updateStatusDiscentes($record, $data);
                    }),
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
            'index' => Pages\ManageDiscentes::route('/'),
        ];
    }
}