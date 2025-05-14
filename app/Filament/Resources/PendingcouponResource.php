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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
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

                            ])
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 4,
                                'xl' => 4,
                                '2xl' => 2,
                            ]),

                        Section::make()
                            ->schema(function (Coupon $record) {
                                return [
                                    $record?->isVirtual()
                                        ? static::getVirtualCouponFieldset()
                                        : static::getPassangersFieldset(),
                                ];
                            })
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 8,
                                'xl' => 8,
                                '2xl' => 4,
                            ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('Jóváhagyás')
                                    ->schema([
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
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->aircrafttype->getLabel()} - {$record->name}"),

                                        ToggleButtons::make('status')
                                            ->helperText('Hagyd jóvá vagy utasítsd el ennek a kuponnak a felhasználást.')
                                            ->label('Válaszd ki kupon státuszát')
                                            ->inline()
                                            ->required()
                                            ->default('0')
                                            ->disabled(fn (Get $get) => Carbon::parse($get('expiration_at')) < today())
                                            ->live()
                                            ->options(CouponStatus::class),

                                    ])->columns(2),

                                Fieldset::make('Érvényesség hosszabbítás')
                                    ->schema([
                                        CustomDatePicker::make('expiration_at')
                                            ->label('Felhasználható')
                                            ->helperText('Itt módosíthatod az adott kupon érvényességi idejét.')
                                            ->prefixIcon('tabler-calendar')
                                            ->weekStartsOnMonday()
                                            ->format('Y-m-d')
                                            ->displayFormat('Y.m.d.')
                                            ->live()
                                            ->default(now()),
                                    ])->columns(2),
                            ])
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 12,
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
                    ->label('Kuponkód')
                    ->wrap()
                    ->color('Amber')
                    ->searchable(['coupon_code', 'source']),
                TextColumn::make('user.name')
                    ->label('Kapcsolattartó')
                    ->formatStateUsing(function ($record) {
                        return $record->user->name.' ('.$record->user->email.')';
                    })
                    ->wrap()
                    ->color('Amber')
                    ->searchable(),
                TextColumn::make('adult')
                    ->label('Utasok')
                    ->formatStateUsing(function ($state, Pendingcoupon $payload) {
                        if (! empty($payload?->adult) && ! empty($payload?->children)) {
                            $passenger_nums = '<p>'.$payload?->adult.'+'.$payload?->children.'</p>';
                        }
                        if (empty($payload?->adult) && ! empty($payload?->children)) {
                            $passenger_nums = '<p>0+'.$payload?->children.'</p>';
                        }
                        if (! empty($payload?->adult) && empty($payload?->children)) {
                            $passenger_nums = '<p>'.$payload?->adult.'+0</p>';
                        }
                        if (empty($payload?->adult) && empty($payload?->children)) {
                            $passenger_nums = '<p>0+0</p>';
                        }

                        if (! empty($payload->description)) {
                            $description = '<p style="font-size:9pt; color:gray;"><b>Megjegyzés: </b>'.$payload->description.'</p>';
                        }
                        if (empty($payload->description)) {
                            $description = '';
                        }
                        $totalpassengermessage = $passenger_nums.$description;

                        return $totalpassengermessage;
                    })->html()
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
                    ->relationship('user', 'name')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return User::where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->select('id', 'name', 'email')->limit(50)->get()->mapWithKeys(function (User $user, int $key) {
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
                Textarea::make('description')
                    ->label('Megjegyzés')
                    ->rows(3)
                    ->cols(20)
                    ->columnSpanFull(),
            ])
            ->columns(3);
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
