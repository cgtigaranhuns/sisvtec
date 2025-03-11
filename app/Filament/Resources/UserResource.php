<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Usuários';

    protected static ?string $navigationGroup = 'Segurança';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Tabs::make('Básico')
                    ->columnSpanFull()
                    ->tabs([                        
                        Tab::make('Básico')
                            ->columns(null)
                            ->icon('heroicon-s-user-circle')  
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),                            
                                // Forms\Components\DateTimePicker::make('email_verified_at'),
                                // Forms\Components\TextInput::make('password')
                                //     ->password()
                                //     ->required()
                                //     ->maxLength(255),
                                
                            ]),
                        Tab::make('Detalhes')
                            ->columns(null)
                            ->icon('heroicon-o-information-circle')                           
                            ->schema([
                                Group::make([
                                    Forms\Components\TextInput::make('matricula')
                                    ->required()
                                    ->maxLength(11),
                                Forms\Components\Select::make('tipo_servidor')
                                    ->label('Tipo de Servidor')
                                    ->required()
                                    ->options([
                                        '1' => 'Professor',
                                        '2' => 'Técnico Administrativo',
                                        
                                    ]),
                                   
                                Forms\Components\Select::make('cargo_id')
                                    ->label('Cargo')
                                    ->required()
                                    ->relationship('cargo', 'nome'),
                                  
                                   
                                    
                                ])
                                ->columns(3)
                                ->relationship('dadosUser')
                                
                                
                            ]),
                    ]),
            ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dadosUser.matricula')
                    ->label('Matrícula')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dadosUser.tipo_servidor')  
                    ->label('Tipo de Servidor')
                    ->badge()
                    ->alignCenter()
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'danger',
                        '2' => 'success',
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state == 1) {
                            return 'Técnico Administrativo';
                        }
                        if ($state == 2) {
                            return 'Professor';
                        }
                    })

                    ->searchable(),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
