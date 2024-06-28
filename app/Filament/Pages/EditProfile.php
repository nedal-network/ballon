<?php
/*
namespace App\Filament\Pages;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Illuminate\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class EditProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.edit-profile';
}
*/

namespace App\Filament\Pages;

use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Concerns;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Auth\Authenticatable;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.pages.edit-profile';
    protected static ?string $title = 'Saját profil';
    protected static ?string $navigationIcon = 'tabler-user-circle';
    //protected static bool $shouldRegisterNavigation = false;
    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();
    }
    protected function getForms(): array
    {
        return [
        'editProfileForm',
        'editPasswordForm',
        ];
    }
    public function editProfileForm(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Felhasználói profil')
            ->description('Itt frissítheted felhasználóneved és a regisztrációhoz használt e-mail címed.')
            ->schema([
            Forms\Components\TextInput::make('name')
            ->label('Név')
            ->helperText('Add meg teljes neved.')
            ->required(),
            Forms\Components\TextInput::make('email')
            ->label('E-mail cím')
            ->helperText('Add meg új e-mail címed pontosan, mert ezt a kapcsolattartási formát használjuk a későbbiekben elsődleges kapcsolattartási formaként.')
            ->email()
            ->required()
            ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('phone')
            ->tel()
            ->label('Telefonszám')
            ->helperText('Add meg új telefonszámod arra az esetre, ha kapcsolatfelvétel tekintetében nem elegendő az e-mailben történő kapcsolatfelvétel')
            ->placeholder('+36_________')
            ->mask('+36999999999')
            ->maxLength(30),
            ])->columns(3),
        ]) 
    ->model($this->getUser())
    ->statePath('profileData');
    }

    public function editPasswordForm(Form $form): Form
    {
        return $form 
        ->schema([
            Forms\Components\Section::make('Jelszó frissítése')
            ->description('Győződj meg róla, hogy fiókod azonosítására hosszú, véletlenszerű jelszót használsz-e a biztonság megőrzése érdekében, vagy állíts be. új saját jelszót.')
            ->schema([
            Forms\Components\TextInput::make('Current password')
            ->label('Jelenlegi jelszó')
            ->helperText('Add meg jelenlegi jelszavad.')
            ->password()
            ->required()
            ->currentPassword(),
            Forms\Components\TextInput::make('password')
            ->label('Új jelszó')
            ->password()
            ->required()
            ->rule(Password::default())
            ->autocomplete('new-password')
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation'),
            Forms\Components\TextInput::make('passwordConfirmation')
            ->label('Új jelszó megerősítése')
            ->password()
            ->required()
            ->dehydrated(false),
            ])->columns(3),
        ])
        ->model($this->getUser())
        ->statePath('passwordData');
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editProfileForm'),
        ];
    }
    protected function getUpdatePasswordFormActions(): array
    {
        return [
            Action::make('updatePasswordAction')
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editPasswordForm'),
        ];
    }

    public function updateProfile(): void
    {
        $data = $this->editProfileForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);
        $this->sendSuccessNotification(); 
    }

    public function updatePassword(): void
    {
        $data = $this->editPasswordForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);
        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put(['password_hash_' . Filament::getAuthGuard() => $data['password']]);
        }
        $this->editPasswordForm->fill();
        $this->sendSuccessNotification(); 
    }

    private function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }

    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();
        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }
        return $user;
    }

    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();
        $this->editProfileForm->fill($data);
        $this->editPasswordForm->fill();
    }

    
    private function sendSuccessNotification(): void
    {
        Notification::make()
        ->success()
        ->title(__('filament-panels::pages/auth/edit-profile.notifications.saved.title'))
        ->send();
    }

}