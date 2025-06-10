<?php

namespace App\Filament\Resources;

use App\Enums\CouponStatus;
use App\Filament\Forms\Components\CustomDatePicker;
use App\Filament\Resources\PendingcouponResource\Pages;
use App\Models\Coupon;
use App\Models\Pendingcoupon;
use App\Models\Tickettype;
use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group as ComponentsGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PendingcouponResource extends Resource
{
    protected static ?string $model = Pendingcoupon::class;

    protected static ?string $navigationIcon = 'tabler-progress-check';

    protected static ?string $modelLabel = 'kupon';

    protected static ?string $pluralModelLabel = 'kuponok';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        ComponentsGroup::make([

                            Section::make()
                                ->schema([
                                    TextInput::make('coupon_code')
                                        ->label('Kupon azonosító 1')
                                        ->prefixIcon('iconoir-password-cursor')
                                        ->placeholder('ABC-'.random_int(100000, 999999))
                                        ->required()
                                        ->minLength(3)
                                        ->maxLength(255)
                                        ->disabledOn('edit'),
                                    TextInput::make('auxiliary_coupon_code')
                                        ->label('Kupon azonosító 2')
                                        ->prefixIcon('iconoir-password-cursor')
                                        ->minLength(3)
                                        ->maxLength(255)
                                        ->disabledOn('edit'),
                                    TextInput::make('source')
                                        ->label('Forrás')
                                        ->required()
                                        ->minLength(3)
                                        ->maxLength(255)
                                        ->disabledOn('edit'),

                                ])->columns(3),
                            Section::make()
                                ->schema(function (Coupon $record) {
                                    return $record?->isVirtual()
                                            ? [static::getVirtualCouponFieldset()]
                                            : [static::getPassangersFieldset()];
                                }),
                            Section::make()
                                ->schema(CouponResource::getActivityLogSchema()),
                        ])->columns(2)
                            ->columnSpan([
                                'sm' => 12,
                                '2xl' => 6,
                            ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('Jóváhagyás')
                                    ->schema([
                                        ComponentsGroup::make([
                                            Select::make('tickettype_id')
                                                ->helperText('Válaszd ki a kívánt jegytípust.')
                                                ->label('Jegytípus')
                                                ->prefixIcon('heroicon-o-ticket')
                                                ->required()
                                                ->native(false)
                                                ->relationship(
                                                    name: 'tickettype',
                                                    modifyQueryUsing: fn (Builder $query) => $query->orderBy('aircrafttype')->orderBy('default', 'desc')->orderBy('name'),
                                                )
                                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->fullname()),
                                            CustomDatePicker::make('expiration_at')
                                                ->label('Felhasználható')
                                                ->helperText('Itt módosíthatod az adott kupon érvényességi idejét.')
                                                ->prefixIcon('tabler-calendar')
                                                ->weekStartsOnMonday()
                                                ->format('Y-m-d')
                                                ->displayFormat('Y.m.d.')
                                                ->afterStateUpdated(function ($state, $record, Get $get, Set $set) {
                                                    if (Carbon::parse($state) < today()) {
                                                        $set('status', CouponStatus::Expired);
                                                    } else {
                                                        $set('status', $get('latest_status') ?? $record->status);
                                                    }
                                                })
                                                ->live()
                                                ->default(now()),
                                        ])->columns(1),
                                        Hidden::make('latest_status')->live()->dehydrated(),
                                        ToggleButtons::make('status')
                                            ->helperText('Hagyd jóvá vagy utasítsd el ennek a kuponnak a felhasználást.')
                                            ->label('Válaszd ki kupon státuszát')
                                            ->inline()
                                            ->required()
                                            ->default('0')
                                            ->disabled(fn (Get $get) => Carbon::parse($get('expiration_at')) < today())
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                if ($state != CouponStatus::Expired) {
                                                    $set('latest_status', $state);
                                                }
                                            })
                                            ->live()
                                            ->options(CouponStatus::class),

                                    ])
                                    ->columns(2),
                                static::getDescriptionTextArea(),
                            ])
                            ->columns(2)
                            ->columnSpan([
                                'sm' => 12,
                                '2xl' => 6,
                            ]),
                    ]),

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->defaultSort('source', 'asc')
            ->defaultGroup(
                Group::make('source')
                    ->getTitleFromRecordUsing(function ($record) {
                        return 'Kibocsájtó: '.$record->source;
                    })
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            )
            ->recordClasses(function (Pendingcoupon $record) {
                $diff_day_nums = Carbon::parse($record->expiration_at)->diffInDays('now', false);
                if ($diff_day_nums > 0 && $diff_day_nums < 31) {
                    return '!bg-yellow-300 !dark:bg-amber-600/30 !hover:bg-yellow-300 !dark:hover:bg-amber-600/30';
                }

            })
            ->columns([
                TextColumn::make('coupon_code')
                    ->label('ID')
                    ->wrap()
                    ->width(1)
                    ->color('Amber')
                    ->searchable(['coupon_code', 'source']),
                TextColumn::make('auxiliary_coupon_code')
                    ->label('ID 2')
                    ->wrap()
                    ->width(1)
                    ->color('Amber')
                    ->searchable(['auxiliary_coupon_code', 'source']),
                TextColumn::make('user.name')
                    ->label('Kapcsolattartó')
                    ->formatStateUsing(fn ($record) => $record->user->name . ($record->user->deleted_at ? ' (törölve)' : ''))
                    ->url(fn (Coupon $record): string => route(UserResource::getRouteBaseName().'.edit', ['record' => $record->user->id]))
                    ->width(1)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->iconColor('success')
                    ->url(fn ($state) => filled($state) ? "mailto:$state" : null)
                    ->width(1)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('adult')
                    ->label('Utasok')
                    ->sortable(false)
                    ->formatStateUsing(fn (Coupon $record) => "{$record->adult}+{$record->children}")
                    ->html()
                    ->description(fn (Coupon $record) => $record->description)
                    ->width(250)
                    ->wrap()
                    ->visibleFrom('md'),
                TextColumn::make('total_price')
                    ->label('Ár')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ').' Ft'),
                TextColumn::make('created_at')
                    ->label('Rögzítve')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat('Y.m.d.'))
                    ->wrap()
                    ->color('Amber'),
                TextColumn::make('expiration_at')
                    ->label('Lejárat')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->translatedFormat('Y.m.d.'))
                    ->color(function ($state) {
                        $diff_day_nums = Carbon::parse($state)->diffInDays('now', false);
                        if ($diff_day_nums > 0 && $diff_day_nums < 31) {
                            return 'primary';
                        }

                    })
                    ->visibleFrom('md'),
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->size('md'),
                TextColumn::make('tickettype_id')
                    ->label('Jegytípus')
                    ->badge()
                    ->color(fn ($record) => \Filament\Support\Colors\Color::Hex($record->tickettype->color))
                    ->formatStateUsing(function ($state, Pendingcoupon $tickettype) {
                        $tickettype_name = Tickettype::find($tickettype->tickettype_id);

                        return $tickettype_name->name;
                    })
                    ->visibleFrom('md'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Kapcsolattartó')
                    ->options(User::withTrashed()->get()->mapWithKeys(function (User $user) {
                        return [$user->id => $user->name.' ('.$user->email.')'];
                    }))
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return User::withTrashed()
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->select('id', 'name', 'email')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function (User $user) {
                                return [$user->id => $user->name.' ('.$user->email.')'];
                            });
                    })
                    ->native(false),
                Filter::make('created_at')
                    ->form([
                        CustomDatePicker::make('created_from')->label('Létrehozási dátumtól')->format('Y-m-d')->displayFormat('Y.m.d.'),
                        CustomDatePicker::make('created_until')->label('Létrehozási dátumig')->format('Y-m-d')->displayFormat('Y.m.d.')->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('source')
                    ->label('Kupon kibocsájtó')
                    ->options([
                        'Ballonozz' => 'Ballonozz.hu',
                        'Meglepkék' => 'Meglepkék',
                        'Egyéb' => 'Egyéb',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
                Tables\Actions\DeleteAction::make()
                    ->label(false)
                    ->tooltip('Törlés')
                    ->action(function ($action) {
                        try {
                            $result = $action->process(static fn (Model $record) => $record->delete());
                        } catch (\Throwable $th) {
                            $result = false;
                            if ($th->errorInfo[0] == 23000 && $th->errorInfo[1] == 1451) { //idegene kulcs miatt nem törölhető
                                $action->failureNotificationTitle('Aktív jelentkezéssel rendelkezik!<br>Nem törölhető!');
                            } else {
                                $action->failureNotificationTitle('Hiba történt!');
                            }
                        } finally {
                            if (! $result) {
                                $action->failure();

                                return;
                            }

                            $action->success();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getAdultsAndChildrenFields(): array
    {
        return [
            TextInput::make('adult')
                ->label('Felnőtt')
                ->prefixIcon('tabler-friends')
                ->numeric()
                ->default(0)
                ->minLength(1)
                ->maxLength(10)
                ->suffix(' fő'),
            TextInput::make('children')
                ->label('Gyermek')
                ->prefixIcon('tabler-horse-toy')
                ->numeric()
                ->default(0)
                ->minLength(1)
                ->maxLength(10)
                ->suffix(' fő'),
        ];
    }

    public static function getDescriptionTextArea(): Textarea
    {
        return Textarea::make('description')
            ->label('Megjegyzés')
            ->rows(3)
            ->cols(20)
            ->columnSpanFull();
    }

    public static function getVirtualCouponFieldset(): Fieldset
    {
        return Fieldset::make('Kiegészítő jegy részletei')
            ->schema([
                ...static::getAdultsAndChildrenFields(),
                TextInput::make('total_price')
                    ->label('Helyszínen fizetendő')
                    ->prefixIcon('iconoir-hand-cash')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minLength(1)
                    ->maxLength(10)
                    ->suffix(' Ft.'),
            ])
            ->columns(3);
    }

    public static function getVirtualCouponSchema(): array
    {
        return [
            static::getVirtualCouponFieldset(),
            static::getDescriptionTextArea(),
        ];
    }

    public static function getPassangersFieldset(): Fieldset
    {
        return Fieldset::make('Utasok száma')
            ->schema(static::getAdultsAndChildrenFields())
            ->columns(2);
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
            'index' => Pages\ListPendingcoupons::route('/'),
            'create' => Pages\CreatePendingcoupon::route('/create'),
            'edit' => Pages\EditPendingcoupon::route('/{record}/edit'),
        ];
    }
}
