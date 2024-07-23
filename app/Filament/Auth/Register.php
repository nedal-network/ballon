<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rules\Password;
use Filament\Pages\Auth\Register as AuthRegister;

class Register extends AuthRegister
{
    public function form(Form $form): Form
    {
        return $form->schema([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            //$this->getPasswordFormComponent(),
            //$this->getPasswordConfirmationFormComponent(),

            TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->helperText('A jelszónak minimum 8 karakternek kell lennie!')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),

            TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false),

            TextInput::make('phone')
            ->label('Telefonszám')
            ->tel()
            ->placeholder('+36_________')
            ->mask('+36999999999')
            ->maxLength(30),
        ])
        ->statePath('data');
    }
}