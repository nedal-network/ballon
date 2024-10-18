<?php

namespace App\Filament\Resources;

use App\Enums\CouponStatus;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'kuponjaim';

    protected static ?string $pluralModelLabel = 'kuponjaim';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(12)
                    ->hiddenOn('create')
                    ->schema([
                        Section::make()
                            ->schema([
                                Grid::make(12)
                                    ->schema([

                                        Fieldset::make()
                                            ->label('Kuponja adatai')
                                            ->schema([
                                                Placeholder::make('coupon_code')
                                                    ->hiddenLabel()
                                                    ->content(function ($record): HtmlString {
                                                        if (empty($record->auxiliary_coupon_code)) {
                                                            return new HtmlString('
                                        <div id="coupon_inline">
                                            <div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="gray" aria-hidden="true" data-slot="icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                                            </svg>
                                            </svg></div><div style="float:left; position:relative;">'.$record->coupon_code.'</div>
                                        </div>
                                        ');
                                                        }
                                                        if (! empty($record->auxiliary_coupon_code)) {
                                                            return new HtmlString('
                                        <div id="coupon_inline">
                                            <div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="gray" aria-hidden="true" data-slot="icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                                            </svg>
                                            </svg></div><div style="float:left; position:relative;">'.$record->coupon_code.'</div>

                                            <br>

                                            <div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="gray" aria-hidden="true" data-slot="icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                                            </svg>
                                            </svg></div><div style="float:left; position:relative;">'.$record->auxiliary_coupon_code.'</div>

                                        </div>
                                        ');
                                                        }
                                                    }),
                                                Placeholder::make('source')
                                                    ->hiddenLabel()
                                                    ->content(function ($record) {
                                                        if (in_array($record->source, ['Ballonozz', 'Meglepkék'])) {
                                                            return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24" stroke="gray">
                                        <path d="M4 9.5C4 14.0714 9.71429 17.5 9.71429 17.5H14.2857C14.2857 17.5 20 14.0714 20 9.5C20 4.92857 16.4183 1.5 12 1.5C7.58172 1.5 4 4.92857 4 9.5Z" stroke="currentColor" stroke-miterlimit="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8.99999 2C5.99996 8 10 17.5 10 17.5" stroke="currentColor" stroke-linejoin="round"/>
                                        <path d="M14.8843 2C17.8843 8 13.8843 17.5 13.8843 17.5" stroke="currentColor" stroke-linejoin="round"/>
                                        <path d="M13.4 23H10.6C10.2686 23 10 22.7314 10 22.4V20.6C10 20.2686 10.2686 20 10.6 20H13.4C13.7314 20 14 20.2686 14 20.6V22.4C14 22.7314 13.7314 23 13.4 23Z" stroke="currentColor" stroke-linecap="round"/>
                                        </svg></div><div style="float:left; position:relative;">'.$record->source.'.hu</div>');
                                                        } else {
                                                            return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="gray" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 16v.01" />
                                        <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                        <path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" />
                                        <path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" />
                                        <path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" />
                                        <path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" />
                                        <path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" />
                                    </svg></div><div style="float:left; position:relative;">'.$record->source.'</div>');
                                                        }

                                                    }),
                                                Placeholder::make('adult')
                                                    ->hiddenLabel()
                                                    ->content(function ($record): HtmlString {
                                                        $extra_adult = 0;
                                                        if ($record->childrenCoupons) {
                                                            $extra_adult += $record->childrenCoupons->map(fn ($coupon) => $coupon->adult)->sum();
                                                        }

                                                        return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="gray" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M5 22v-5l-1 -1v-4a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4l-1 1v5" />
                                    <path d="M17 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M15 22v-4h-2l2 -6a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1l2 6h-2v4" />
                                </svg></div><div style="float:left; position:relative;">'.$record->adult.($extra_adult > 0 ? '+'.$extra_adult : '').' fő</div>');
                                                    }),
                                                Placeholder::make('children')
                                                    ->hiddenLabel()
                                                    ->content(function ($record): HtmlString {
                                                        $extra_children = 0;
                                                        if ($record->childrenCoupons) {
                                                            $extra_children += $record->childrenCoupons->map(fn ($coupon) => $coupon->children)->sum();
                                                        }

                                                        return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="gray" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3.5 17.5c5.667 4.667 11.333 4.667 17 0" />
                                    <path d="M19 18.5l-2 -8.5l1 -2l2 1l1.5 -1.5l-2.5 -4.5c-5.052 .218 -5.99 3.133 -7 6h-6a3 3 0 0 0 -3 3" />
                                    <path d="M5 18.5l2 -9.5" />
                                    <path d="M8 20l2 -5h4l2 5" />
                                </svg></div><div style="float:left; position:relative;">'.$record->children.($extra_children > 0 ? '+'.$extra_children : '').' fő</div>');
                                                    }),
                                                Placeholder::make('expiration_at')
                                                    ->hiddenLabel()
                                                    ->content(function ($record): HtmlString {
                                                        return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="gray" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M11 15h1" />
                                    <path d="M12 15v3" />
                                    </svg></div><div style="float:left; position:relative;">'.Carbon::parse($record->expiration_at)->translatedFormat('Y.m.d.').'</div>');
                                                    }),
                                            ])
                                            ->columns([
                                                'sm' => 5,
                                                'md' => 5,
                                                'lg' => 5,
                                                'xl' => 5,
                                                '2xl' => 5,
                                            ]),

                                    ]),
                            ])
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 7,
                                '2xl' => 7,
                            ]),

                        Section::make()
                            ->schema([
                                Grid::make(12)
                                    ->schema([

                                        Fieldset::make()
                                            ->label('Kuponok öszvonása / szétválasztása')
                                            ->schema([
                                                Select::make('custom_children_ids')
                                                    ->label('Válasszon kuponjai közül')
                                                    ->multiple()
                                                    ->options(function ($record) {
                                                        $coupons = Coupon::whereIn('status', [1, 2])->where('coupon_code', '!=', $record->coupon_code)->where('source', '!=', 'Kiegészítő')->doesntHave('childrenCoupons')->get();
                                                        foreach ($coupons as $coupon) {
                                                            $filteredcoupons[$coupon->id] = 'Kuponkód: '.$coupon->coupon_code.' -> (felnőtt: '.$coupon->adult.' fő, gyermek: '.$coupon->children.' fő)';
                                                        }

                                                        return $filteredcoupons ?? [];
                                                    })
                                                    ->preload(),

                                                Actions::make([Forms\Components\Actions\Action::make('merge_coupons')
                                                    ->label('Kuponok összevonása / szétválasztása')
                                                    ->extraAttributes(['type' => 'submit'])
                                                    ->action(
                                                        function ($livewire, $record) {
                                                            $data = $livewire->form->getState();

                                                            $virtualCoupons = $record->childrenCoupons->where('source', 'Kiegészítő');

                                                            // Maradék utasok száma
                                                            $mod = $record->passengers->count() - ($virtualCoupons->sum('members_count') + $record->adult + $record->children);

                                                            $modPassengers = collect([]);

                                                            if ($mod > 0) {
                                                                // A szülő kupon utasai közül kiválasztjuk az utolsó $mod utast
                                                                $modPassengers = $record->passengers->slice($record->passengers->count() - $mod, $mod); // maradék utasok
                                                            }

                                                            // Az öszes gyerek kupon ID kivéve a virtuálisak
                                                            $childrenCouponIds = $record->childrenCoupons
                                                                ->whereNotIn('id', $virtualCoupons->pluck('id'))
                                                                ->pluck('id')
                                                                ->toArray();

                                                            $separableCouponIds = array_diff($childrenCouponIds, $data['custom_children_ids']); // Szétválaszható kupon ID-k
                                                            $mergeableCouponIds = array_diff($data['custom_children_ids'], $childrenCouponIds); // Összevonható kupon ID-k

                                                            // Kuponok szétválasztása
                                                            $index = 0;
                                                            foreach ($record->childrenCoupons->whereIn('id', $separableCouponIds) as $childCoupon) {
                                                                $childCoupon->update(['parent_id' => null]); // kupon leválasztása a szülőről

                                                                $passengers = $modPassengers->slice($index, $childCoupon->membersCount); // kiválasztunk annyi utast amennyi utasa lehet a kuponnak

                                                                $childCoupon->passengers()->saveMany($passengers);  // áthelyezzük az utasokat

                                                                $record->passengers()->whereIn('id', $passengers->pluck('id'))->delete(); // töröljük a volt szülő kuponból az áthelyezett utasokat

                                                                $index += $childCoupon->membersCount;
                                                            }

                                                            // Kuponok öszvonása
                                                            $mergeableCoupons = Coupon::whereIn('id', $mergeableCouponIds)->get();

                                                            if ($mergeableCoupons->count()) {

                                                                foreach ($mergeableCoupons as $coupon) {
                                                                    $coupon->passengers()->update(['coupon_id' => $record->id]);
                                                                }

                                                                $record->childrenCoupons()->saveMany($mergeableCoupons);
                                                            }

                                                            $record->refresh();
                                                        }),
                                                ]),

                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'md' => 1,
                                                'lg' => 1,
                                                'xl' => 1,
                                                '2xl' => 1,
                                            ]),

                                        Fieldset::make()
                                            ->label('Kiegészítő jegy(ek)')
                                            ->schema([
                                                Placeholder::make('coupon_code')
                                                    ->hiddenLabel()
                                                    ->content(function ($record) {
                                                        $virtualcoupons = Coupon::where('parent_id', '=', $record->id)->where('source', '=', 'Kiegészítő')->get();
                                                        foreach ($virtualcoupons as $virtualcoupon) {
                                                            $filteredvirtualcoupons[$virtualcoupon->id] = 'Kuponkód: '.$virtualcoupon->coupon_code.' -> (felnőtt: '.$virtualcoupon->adult.' fő, gyermek: '.$virtualcoupon->children.' fő)';
                                                        }

                                                        return new HtmlString(implode(',<br>', $filteredvirtualcoupons ??= []));
                                                    }),

                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'md' => 1,
                                                'lg' => 1,
                                                'xl' => 1,
                                                '2xl' => 1,
                                            ]),

                                    ]),
                            ])
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 5,
                                '2xl' => 5,
                            ]),
                    ])->columns(12),

                Grid::make(12)
                    ->hiddenOn('edit')
                    ->schema([
                        Section::make()
                            ->hiddenOn('edit')
                            ->schema([
                                Grid::make(12)
                                    ->schema([
                                        Section::make()
                                            ->schema([
                                                TextInput::make('coupon_code')
                                                    /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                                    ->helperText(function (GET $get) {
                                                        switch ($get('source')) {
                                                            case 'Ballonozz':
                                                                return 'Megrendelési azonosító (#1234) csak szám része (1234)';
                                                            case 'Meglepkék':
                                                                return '7 jegyű kupon kód (AB12345)';
                                                            case 'Élménypláza':
                                                                return 'Voucher kód (123456)';
                                                            case 'ÉljAMának':
                                                                return '3x5 jegyű azonosító (12345-12345-12345)';
                                                            case 'Aji kártya':
                                                            case 'Feldobox':
                                                                return '10 jegyű azonosító (0123456789)';
                                                            default:
                                                                return '';
                                                        }
                                                    })
                                                    ->label('Kupon azonosító 1')
                                                    ->prefixIcon('iconoir-password-cursor')
                                                    ->required()
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->disabledOn('edit'),
                                                TextInput::make('auxiliary_coupon_code')
                                                /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                                    ->helperText(function (GET $get) {
                                                        switch ($get('source')) {
                                                            case 'Ballonozz':
                                                            case 'Meglepkék':
                                                            case 'ÉljAMának':
                                                            case 'Feldobox':
                                                                return 'Hagyd üresen';
                                                            case 'Élménypláza':
                                                                return 'Voucher biztonsági kód (123456)';
                                                            case 'Aji kártya':
                                                                return '4 jegyű pin kód';
                                                            default:
                                                                return '';
                                                        }
                                                    })
                                                    ->label('Kupon azonosító 2')
                                                    ->prefixIcon('iconoir-password-cursor')
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->disabledOn('edit'),
                                            ])//->columnSpan(4),
                                            ->columnSpan([
                                                'sm' => 12,
                                                'md' => 12,
                                                'lg' => 6,
                                                'xl' => 6,
                                                '2xl' => 6,
                                            ]),
                                        Section::make()
                                            ->schema([
                                                Select::make('source')
                                                    ->label('Kupon kibocsátója')
                                                    ->helperText('Válaszd ki honnan származik az adott kupon.')
                                                    ->options([
                                                        'Ballonozz' => 'Ballonozz.hu',
                                                        'Meglepkék' => 'Meglepkék',
                                                        'Élménypláza' => 'Élménypláza',
                                                        'ÉljAMának' => 'ÉljAMának',
                                                        'Aji kártya' => 'Aji kártya',
                                                        'Feldobox' => 'Feldobox',
                                                        'Egyéb' => 'Egyéb',
                                                    ])
                                                    ->required()
                                                    ->default('Ballonozz')
                                                    ->disabledOn('edit')
                                                    ->live()
                                                    ->native(false),
                                            ])//->columnSpan(8),
                                            ->columnSpan([
                                                'sm' => 12,
                                                'md' => 12,
                                                'lg' => 6,
                                                'xl' => 6,
                                                '2xl' => 6,
                                            ]),
                                    ]),

                            ])//->columnSpan(6),
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 6,
                                '2xl' => 6,
                            ])->columns(6),

                        Section::make()
                            ->hidden(fn (GET $get, $operation): bool => (in_array($get('source'), ['Ballonozz', 'Meglepkék']) && $operation == 'create'))
                            ->schema([
                                Fieldset::make('Utasok száma')
                                    ->schema([
                                        TextInput::make('adult')
                                            ->helperText('Add meg a kuponhoz tartozó felnőtt utasok számát.')
                                            ->label('Felnőtt')
                                            ->prefixIcon('tabler-friends')
                                            //->required()
                                            ->disabledOn('edit')
                                            ->numeric()
                                            ->default(0)
                                            //->minValue(1)
                                            ->minLength(1)
                                            ->maxLength(10)
                                            ->suffix(' fő'),
                                        TextInput::make('children')
                                            ->helperText('Add meg a kuponhoz tartozó gyermek utasok számát.')
                                            ->label('Gyermek')
                                            ->prefixIcon('tabler-horse-toy')
                                            ->disabledOn('edit')
                                            ->numeric()
                                            ->default(0)
                                            ->minLength(1)
                                            ->maxLength(10)
                                            ->suffix(' fő'),
                                    ])//->columns(2)
                                    ->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ]),
                                Fieldset::make('Érvényesség')
                                    ->schema([
                                        DatePicker::make('expiration_at')
                                            ->label('Kupon lejárati dátum')
                                            ->helperText('Add meg a kuponon szereplő lejárti dátumot.')
                                            ->prefixIcon('tabler-calendar')
                                            ->weekStartsOnMonday()
                                            ->format('Y-m-d')
                                            ->displayFormat('Y-m-d')
                                            ->default(function () {
                                                return Carbon::today()->addDay();
                                            })
                                            ->minDate(function () {
                                                return Carbon::today()->addDay();
                                            })
                                            ->disabledOn('edit'),
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ]),

                            ])//->columnSpan(4),
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 6,
                                '2xl' => 6,
                            ]),
                        //Hidden::make('status')->default('0'),
                    ])->columns(12),

                Grid::make(12)
                    ->visible(fn (GET $get, $operation, $record) => $operation == 'edit' && ($record->membersCount > 0))
                    ->schema([
                        Section::make()
                            ->extraAttributes(fn ($record) => $record->missing_data ? ['style' => 'border: 1px solid #ff0000;'] : [])
                            ->schema([
                                Repeater::make('passengers')
                                    ->addActionLabel('Új utas felvétele')
                                    ->label('Utasok')
                                    ->relationship()
                                    ->maxItems(fn ($record) => $record->membersCount)
                                    ->schema([
                                        Fieldset::make('Kötelező utasadatok')
                                            ->schema([
                                                TextInput::make('lastname')
                                                    ->disabledOn('create')
                                                    ->label('Vezetéknév')
                                                    ->prefixIcon('tabler-writing-sign')
                                                    ->placeholder('pl.: Gipsz')
                                                    ->required()
                                                    ->minLength(3)
                                                    ->maxLength(255),
                                                TextInput::make('firstname')
                                                    ->label('Keresztnév')
                                                    ->prefixIcon('tabler-writing-sign')
                                                    ->placeholder('Jakab')
                                                    ->minLength(3)
                                                    ->maxLength(255)
                                                    ->extraInputAttributes(fn ($state) => $state == '' ? ['style' => 'background-color: #ff00004d'] : []),
                                                DatePicker::make('date_of_birth')
                                                    ->label('Születési dátum')
                                                    ->prefixIcon('tabler-calendar')
                                                    ->weekStartsOnMonday()
                                                    ->displayFormat('Y-m-d')
                                                    ->extraInputAttributes(fn ($state) => $state == '' ? ['style' => 'background-color: #ff00004d'] : []),
                                                TextInput::make('id_card_number')
                                                    ->label('Igazolvány szám')
                                                    ->prefixIcon('tabler-id')
                                                    ->placeholder('432654XX')
                                                    ->minLength(3)
                                                    ->maxLength(10)
                                                    ->extraInputAttributes(fn ($state) => $state == '' ? ['style' => 'background-color: #ff00004d'] : []),
                                                TextInput::make('body_weight')
                                                    ->label('Testsúly')
                                                    ->prefixIcon('iconoir-weight-alt')
                                                    ->numeric()
                                                    ->minLength(1)
                                                    ->maxLength(10)
                                                    ->suffix(' kg')
                                                    ->extraInputAttributes(fn ($state) => $state == '' ? ['style' => 'background-color: #ff00004d'] : []),
                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'md' => 2,
                                                'lg' => 3,
                                                'xl' => 5,
                                                '2xl' => 5,
                                            ]),
                                        Fieldset::make('Opcionális utasadatok')
                                            ->schema([
                                                Placeholder::make('created')
                                                    ->label('')
                                                    ->content('Kérjük add meg az elérhetőségedet, az esetleges, fontos kapcsolatfelvétel céljából. Az itt megadott adatait csak és kizárólag fontos, eseménybeni változásokkor használjuk.'),
                                                TextInput::make('email')
                                                    ->email()
                                                    ->label('Email cím')
                                                    ->prefixIcon('tabler-mail-forward')
                                                    ->placeholder('utas@repulnifogok.hu')
                                                    ->maxLength(255),
                                                TextInput::make('phone')
                                                    ->tel()
                                                    ->label('Telefonszám')
                                                    ->prefixIcon('tabler-device-mobile')
                                                    ->placeholder('+36_________')
                                                    ->mask('+99999999999')
                                                    ->maxLength(30),
                                            ])
                                            ->columns([
                                                'sm' => 1,
                                                'md' => 2,
                                                'lg' => 3,
                                                'xl' => 3,
                                                '2xl' => 3,
                                            ]),

                                    ])->columns(5),
                            ])
                            ->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 12,
                                '2xl' => 12,
                            ]),
                    ])
                    ->columns(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordClasses(function (Coupon $record) {
                if ($record->parent_id != null) {
                    return 'bg-gray-300/70 dark:bg-gray-600/30';
                }

            })
            ->columns([
                IconColumn::make('missing_data')
                    ->label('')
                    ->width(0)
                    ->boolean()
                    ->trueIcon('tabler-alert-triangle')
                    ->size(IconColumn\IconColumnSize::Medium)
                    ->trueColor('danger')
                    ->falseIcon('')
                    ->tooltip(fn ($state) => $state ? 'Hiányzó utasadatok!' : ''),
                IconColumn::make('childrenCoupons')
                    ->label('')
                    ->width(0)
                    ->boolean()
                    ->trueIcon('tabler-ticket')
                    ->size(IconColumn\IconColumnSize::Large)
                    ->trueColor('warning')
                    ->falseIcon('')
                    ->tooltip(fn ($state, $record) => $state ? 'Összevonva az alábbi kupon(ok)al: '.implode(', ', $state->pluck('coupon_code')->toArray()) : ''),
                TextColumn::make('coupon_code')
                    ->label('Kupon azonosító 1')
                    ->wrap()
                    ->color('Amber')
                    ->searchable(),
                TextColumn::make('auxiliary_coupon_code')
                    ->label('Kupon azonosító 2')
                    ->wrap()
                    ->color('Amber')
                    ->searchable(),
                TextColumn::make('tickettype.name')
                    ->label('Jegytípus')
                    ->searchable(),
                TextColumn::make('source')
                    ->label('Kibocsátó')
                    ->searchable(),
                TextColumn::make('adult')
                    ->label('Utasok')
                    ->formatStateUsing(function ($state, Coupon $payload) {
                        $extra_adult = 0;
                        $extra_children = 0;
                        if ($payload->childrenCoupons) {
                            $extra_adult += $payload->childrenCoupons->map(fn ($coupon) => $coupon->adult)->sum();
                            $extra_children += $payload->childrenCoupons->map(fn ($coupon) => $coupon->children)->sum();
                        }

                        return '<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$payload->adult.($extra_adult > 0 ? '+'.$extra_adult : '').'</span><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;"> felnőtt</span> <span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$payload->children.($extra_children > 0 ? '+'.$extra_children : '').'</span><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;"> gyerek</span></p>';
                    })->html()
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('expiration_at')
                    ->label('Lejárat')
                    ->formatStateUsing(function ($state) {
                        return Carbon::parse($state)->translatedFormat('Y.m.d.');
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->size('md'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->label(false)->tooltip('Törlés')
                    ->hidden(fn ($record) => ($record->status == CouponStatus::Used || $record->status == CouponStatus::Expired || $record->status == CouponStatus::Applicant || $record->parent_id != null)),
            ])
            ->headerActions([
            ])
            ->recordUrl(
                /* így is lehet
                fn (Coupon $record): string => ($record->status==CouponStatus::Used) ?false: route('filament.admin.resources.coupons.edit', ['record' => $record]),
                vagy úgy ahogy ez alatt van */
                function ($record) {
                    if ($record->status == CouponStatus::Used || $record->status == CouponStatus::Expired || $record->status == CouponStatus::Applicant || $record->parent_id != null) {
                        return false;
                    } else {
                        return route('filament.admin.resources.coupons.edit', ['record' => $record]);
                    }
                },
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Mind törlése'),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('parent_id'))
            ->defaultGroup(
                Group::make('status')
                    ->getTitleFromRecordUsing(function ($record) {
                        return 'Státusz: '.$record->status->getLabel();
                    })
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            );
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi publikált repülési terv van
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', '1')->orwhere('status', '2')->count();
    }
}
