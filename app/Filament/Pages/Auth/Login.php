<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Forms\Components\Component;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class Login extends BaseLogin
{
    public function getHeading(): string|\Illuminate\Support\HtmlString
    {
        return 'SISVTEC';
    }

    public function getSubheading(): string|\Illuminate\Support\HtmlString
    {
        return 'Sistema de Atividades Extraclasse';
    }

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
                })->validationMessages([
                    'required' => 'O campo usuário é obrigatório',
                ]),
            
            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->revealable()
                ->required()
                ->validationMessages([
                    'required' => 'O campo senha é obrigatório',
                ]),
            
            Hidden::make('ldap_connection') // Campo oculto
                ->default('adm'), // Valor padrão
            
            $this->getRememberFormComponent(),
        ])
        ->statePath('data');
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
            'connection' => $data['ldap_connection'], // Adiciona a conexão selecionada
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
            'data.password' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $e) {
            session()->flash(
                'errors',
                array_merge(session()->get('errors', []), $e->errors())
            );

            throw $e;
        }
    }
}