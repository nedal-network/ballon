<?php

namespace App\Filament\Resources;

use App\Enums\CouponStatus;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

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
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->rule(Password::default())
                        ->confirmed()
                        ->reactive(),
                    TextInput::make('password_confirmation')->label('Jelszó megerősítése')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->disabled(fn (\Filament\Forms\Get $get) => ! filled($get('password'))),
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
                    ]),
            ])
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->searchable(),
                TextColumn::make('name')->label('Név')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefonszám')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Regisztrált')
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->translatedFormat('Y.m.d.');
                    }),
                TextColumn::make('last_login_at')
                    ->label('Utoljára itt')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat('Y.m.d.')),

                TextColumn::make('coupons')->label('Kuponok')
                    ->formatStateUsing(fn ($record) => collect(CouponStatus::cases())
                        ->filter(fn ($status) => $status !== CouponStatus::Applicant)
                        ->map(fn ($status) => $record->coupons->where('status', $status)->count())
                        ->implode(', ')
                    )
                    ->tooltip(collect(CouponStatus::cases())
                        ->filter(fn ($status) => $status !== CouponStatus::Applicant)
                        ->map(fn ($status) => $status->getLabel())
                        ->implode(', ')
                    ),
                TextColumn::make('roles.name')->label('Jogosultságok')
                    ->badge()
                    ->label(__('Role'))
                    ->colors(['primary'])
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('kupon_filter')->label('Kuponok')
                    ->url(fn ($record): string => route('filament.admin.resources.pendingcoupons.index').'?tableFilters[user_id][value]='.$record->id),
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
                Impersonate::make()
                    ->tooltip('Átjelentkezés')
                    ->redirectTo(route('filament.admin.pages.dashboard'))
                    ->icon('tabler-ghost-2'),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
