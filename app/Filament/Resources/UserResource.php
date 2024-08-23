<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Coupon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use App\Filament\Exports\UserExporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use App\Filament\Resources\UserResource\Pages;
use Filament\Actions\Exports\Enums\ExportFormat;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $modelLabel = 'felhasználó';
    protected static ?string $pluralModelLabel = 'felhasználók';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-shield::filament-shield.nav.group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Név')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('phone')
                ->tel()
                ->label('Telefonszám')
                ->placeholder('+36_________')
                ->mask('+36999999999')
                ->maxLength(30),
            
                Group::make()->schema([
                    TextInput::make('password')->label('Jelszó')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->rule(Password::default())
                        ->confirmed()
                        ->reactive(),
                    TextInput::make('password_confirmation')->label('Jelszó megerősítése')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->disabled(fn(\Filament\Forms\Get $get) => !filled($get('password'))),
                ])->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 2,
                    'xl' => 2,
                    '2xl' => 2,
                    ]),
                Select::make('roles')->label('Jogosultságok')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->label('Felhasználók exportálása')
                    ->exporter(UserExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
            ])
            ->columns([
                TextColumn::make('name')->label('Név')
                ->searchable(),
                TextColumn::make('email')
                ->searchable(),
                TextColumn::make('phone')
                ->label('Telefonszám')
                ->searchable(),
                TextColumn::make('created_at')
                ->label('Regisztrált')
                ->formatStateUsing(function ($state){
                    return Carbon::parse($state)->translatedFormat('Y.m.d.');
                }),
                TextColumn::make('last_login_at')
                ->label('Utoljára itt')
                ->formatStateUsing(function ($state){
                    $last_date = Carbon::parse($state)->translatedFormat('Y.m.d.');
                    $diff_day_nums = Carbon::parse($state)->diffInDays('now', false);
                    if ($diff_day_nums == 0){return $last_date.', mai napon';}
                    if ($diff_day_nums != 0)
                    {
                        return $last_date.', '.abs($diff_day_nums).($diff_day_nums < 0 ? : ' napja');
                    }
                }),
                
                TextColumn::make('coupons')
                /*
                ->formatStateUsing(function($record)
                {
                    $coupoAllNums = Coupon::where('user_id', $record->id)->get()->count();
                    $couponStatusUnderProcess = Coupon::where('user_id', $record->id)->where('status', 0)->get()->count();
                    $couponStatusCanBeUsed = Coupon::where('user_id', $record->id)->where('status', 1)->orwhere('status', 2)->get()->count();
                    $couponStatusUsed = Coupon::where('user_id', $record->id)->where('status', 3)->get()->count();
                    $couponStatusExpired = Coupon::where('user_id', $record->id)->where('status', 4)->get()->count();
                    return '<p class="text-xs text-gray-500 dark: text-xs text-gray-400"><b>Összesen:</b> '.$coupoAllNums.' kupon, ebből:</p>
                    <p class="text-xs text-gray-500 dark: text-xs text-gray-400"><b>Feldolgozás alatt:</b> '.$couponStatusUnderProcess.' kupon</p>
                    <p class="text-xs text-gray-500 dark: text-xs text-gray-400"><b>Felhasználható:</b> '.$couponStatusCanBeUsed.' kupon</p>
                    <p class="text-xs text-gray-500 dark: text-xs text-gray-400"><b>Felhasznált:</b> '.$couponStatusUsed.' kupon</p>
                    <p class="text-xs text-gray-500 dark: text-xs text-gray-400"><b>Lejárt:</b> '.$couponStatusExpired.' kupon</p>
                    ';
                })
                ->html()
                */
                ->formatStateUsing(function($record)
                {
                    $couponStatusUnderProcess = Coupon::where('user_id', $record->id)->where('status', 0)->get()->count();
                    $couponStatusCanBeUsed = Coupon::where('user_id', $record->id)->where('status', 1)->orwhere('status', 2)->get()->count();
                    $couponStatusUsed = Coupon::where('user_id', $record->id)->where('status', 3)->get()->count();
                    $couponStatusExpired = Coupon::where('user_id', $record->id)->where('status', 4)->get()->count();
                    return $couponStatusUnderProcess.', '.$couponStatusCanBeUsed.', '.$couponStatusUsed.', '.$couponStatusExpired;
                })
                ->tooltip('Feldolgozás alatt, Felhasználható, Felhasznált, Lejárt')
                ->label('Kuponok'),
                TextColumn::make('roles.name')->label('Jogosultságok')
                    ->badge()
                    ->label(__('Role'))
                    ->colors(['primary'])
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
                Impersonate::make()
                ->tooltip('Átjelentkezés')
                ->redirectTo(route('filament.admin.pages.dashboard'))
                ->icon('tabler-ghost-2'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
