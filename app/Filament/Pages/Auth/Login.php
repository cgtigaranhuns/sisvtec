<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('username')
                ->label('Usuário')
                ->required()
                ->autocomplete()
                ->autofocus()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $connection = strlen($state) < 11 ? 'adm' : 'labs';
                    $set('ldap_connection', $connection);
                }),
            
            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->required(),
            
            Hidden::make('ldap_connection') // Campo oculto
                ->default('adm'), // Valor padrão
            
            $this->getRememberFormComponent(),
        ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
            'connection' => $data['ldap_connection'], // Adiciona a conexão selecionada
        ];
    }
}