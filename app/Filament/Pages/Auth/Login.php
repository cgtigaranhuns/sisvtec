<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;


class Login extends BaseAuth
{

    public function getHeading(): string|Htmlable
    {
        return 'SISVT';
    }

    public function getSubheading(): string|Htmlable
    {
        return 'Sistema de Visita Técnica';
    }

   

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            
              //  $this->getEmailFormComponent(),
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
               
            ])
            ->statePath('data');
    }

    
    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Usuário')
            ->required()
            ->autocomplete()
            ->autofocus();
    }

   
    
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password'  => $data['password'],
        ];
    }
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->addError('username', __('filament::login.messages.throttled', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => ceil($exception->secondsUntilAvailable / 60),
            ]));

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt([
            'samaccountname' => $data['username'], /*changed to samaccountname */
            'password' => $data['password'],
        ], $data['remember'])) {
            $this->addError('username', __('filament::login.messages.failed'));

            return null;
        }

        return app(LoginResponse::class);
    }
}
