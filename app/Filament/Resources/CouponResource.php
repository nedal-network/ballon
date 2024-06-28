<?php

namespace App\Filament\Resources;
use Closure;
use Filament\Forms;
use Filament\Tables;
use Faker\Core\Color;
use App\Models\Coupon;
use Faker\Core\Number;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Passenger;
use App\Models\Tickettype;
use Filament\Tables\Table;
use App\Enums\CouponStatus;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use App\Models\CouponCodeAttempt;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Grid;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\CouponResource\Pages;
use AnourValar\EloquentSerialize\Tests\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CouponResource\RelationManagers;
use Laravel\SerializableClosure\Serializers\Native;

use function PHPUnit\Framework\isNull;

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
                                ->content(function($record): HtmlString {
                                    if (empty($record->auxiliary_coupon_code))
                                    {
                                        return new HtmlString('
                                        <div id="coupon_inline">
                                            <div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="gray" aria-hidden="true" data-slot="icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                                            </svg>
                                            </svg></div><div style="float:left; position:relative;">'. $record->coupon_code.'</div>
                                        </div>
                                        ');
                                    }
                                    if (!empty($record->auxiliary_coupon_code))
                                    {
                                        return new HtmlString('
                                        <div id="coupon_inline">
                                            <div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="gray" aria-hidden="true" data-slot="icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                                            </svg>
                                            </svg></div><div style="float:left; position:relative;">'. $record->coupon_code.'</div>
                                            
                                            <br>

                                            <div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="gray" aria-hidden="true" data-slot="icon">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/>
                                            </svg>
                                            </svg></div><div style="float:left; position:relative;">'. $record->auxiliary_coupon_code.'</div>

                                        </div>
                                        ');
                                    }
                                }),
                                Placeholder::make('source')
                                ->hiddenLabel()
                                ->content(function($record){
                                    if($record->source == 'Ballonozz')
                                    {
                                        return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;">
                                        <svg width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24" stroke="gray">
                                        <path d="M4 9.5C4 14.0714 9.71429 17.5 9.71429 17.5H14.2857C14.2857 17.5 20 14.0714 20 9.5C20 4.92857 16.4183 1.5 12 1.5C7.58172 1.5 4 4.92857 4 9.5Z" stroke="currentColor" stroke-miterlimit="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8.99999 2C5.99996 8 10 17.5 10 17.5" stroke="currentColor" stroke-linejoin="round"/>
                                        <path d="M14.8843 2C17.8843 8 13.8843 17.5 13.8843 17.5" stroke="currentColor" stroke-linejoin="round"/>
                                        <path d="M13.4 23H10.6C10.2686 23 10 22.7314 10 22.4V20.6C10 20.2686 10.2686 20 10.6 20H13.4C13.7314 20 14 20.2686 14 20.6V22.4C14 22.7314 13.7314 23 13.4 23Z" stroke="currentColor" stroke-linecap="round"/>
                                        </svg></div><div style="float:left; position:relative;">'.$record->source.'.hu</div>');
                                    }
                                    if($record->source == 'Egyéb')
                                    {
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
                                ->content(function($record): HtmlString {
                                    return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="gray" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M5 22v-5l-1 -1v-4a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4l-1 1v5" />
                                    <path d="M17 5m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M15 22v-4h-2l2 -6a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1l2 6h-2v4" />
                                </svg></div><div style="float:left; position:relative;">'.$record->adult.' fő</div>');
                                }),
                                Placeholder::make('children')
                                ->hiddenLabel()
                                ->content(function($record): HtmlString {
                                    return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="gray" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3.5 17.5c5.667 4.667 11.333 4.667 17 0" />
                                    <path d="M19 18.5l-2 -8.5l1 -2l2 1l1.5 -1.5l-2.5 -4.5c-5.052 .218 -5.99 3.133 -7 6h-6a3 3 0 0 0 -3 3" />
                                    <path d="M5 18.5l2 -9.5" />
                                    <path d="M8 20l2 -5h4l2 5" />
                                </svg></div><div style="float:left; position:relative;">'.$record->children.' fő</div>');
                                }),
                                Placeholder::make('expiration_at')
                                ->hiddenLabel()
                                ->content(function($record): HtmlString {
                                    return new HtmlString('<div style="float:left; position:relative; margin-right: 6px;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="gray" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M11 15h1" />
                                    <path d="M12 15v3" />
                                    </svg></div><div style="float:left; position:relative;">'. Carbon::parse($record->expiration_at)->translatedFormat('Y.m.d.').'</div>');
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
                            ->label('Kuponok öszvonása')
                            ->schema([
                                Select::make('custom_children_ids')
                                ->label('Válasszon kuponjai közül')
                                ->multiple()
                                ->options(function($record){
                                    $coupons = Coupon::whereIn('status', [1, 2])->where('coupon_code', '!=', $record->coupon_code)->where('source', '!=', 'Kiegészítő')->doesntHave('childrenCoupons')->get();
                                    foreach ($coupons as $coupon) {
                                            $filteredcoupons[$coupon->id] = 'Kuponkód: '.$coupon->coupon_code.' -> (felnőtt: '.$coupon->adult.' fő, gyermek: '.$coupon->children.' fő)';
                                    }
                                    return $filteredcoupons ?? [];
                                })
                                ->preload(),

                                Actions::make([Forms\Components\Actions\Action::make('merge_coupons')
                                ->label('Kupon(ok) összevonása ezzel a kuponnal')
                                ->extraAttributes(['type'=>'submit'])
                                ->action(
                                    function($livewire, $record)
                                    {
                                        //dd($data = $livewire->form->getState());
                                        //Coupon::where('coupon_code', $data['children_coupon'])->update(['parent_coupon'=>$record->coupon_code]);
                                        //$record->childrenCoupons()->save(Coupon::find($data['children_coupon']));
                                        $datas = $livewire->form->getState();
                                        Coupon::where('parent_id', $record->id)->where('source', '!=', 'Kiegészítő')->update(['parent_id' => null]);
                                        foreach ($datas['custom_children_ids'] as $id) {
                                            $record->childrenCoupons()->save(Coupon::find($id));
                                        }
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
                                ->content(function($record){
                                    $virtualcoupons = Coupon::where('parent_id', '=', $record->id)->where('source', '=', 'Kiegészítő')->get();
                                    foreach ($virtualcoupons as $virtualcoupon)
                                    {
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
                                                ->helperText('Add meg a kupon azonosító 1-et.')
                                                ->label('Kupon azonosító 1')
                                                ->prefixIcon('iconoir-password-cursor')
                                                ->placeholder('ABC-'. random_int(100000, 999999))
                                                ->required()
                                                ->minLength(3)
                                                ->maxLength(255)
                                                ->disabledOn('edit'),
                                                TextInput::make('auxiliary_coupon_code')
                                                /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                                ->helperText('Add meg a kupon azonosító 2-őt.')
                                                ->label('Kupon azonosító 2')
                                                ->prefixIcon('iconoir-password-cursor')
                                                ->placeholder('ABC-'. random_int(100000, 999999))
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
                                            /*
                                            ToggleButtons::make('source')
                                                ->helperText('Válaszd ki honnan származik az adott kupon.')
                                                ->label('Válassza ki kuponjának forrását')
                                                ->inline()
                                                ->required()
                                                ->default('Ballonozz')
                                                ->disabledOn('edit')
                                                ->live()
                                                ->options([
                                                    //'Meglepkék' => 'Meglepkék',
                                                    'Ballonozz' => 'Ballonozz.hu',
                                                    'Egyéb' => 'Egyéb',
                                                ])
                                                ->icons([
                                                    //'Meglepkék' => 'tabler-butterfly',
                                                    'Ballonozz' => 'iconoir-hot-air-balloon',
                                                    'Egyéb' => 'tabler-progress-help',
                                                ])
                                                ->colors([
                                                    //'Meglepkék' => 'info',
                                                    'Ballonozz' => 'info',
                                                    'Egyéb' => 'info',
                                                ]),
                                                */
                                                /*
                                                Actions::make([Forms\Components\Actions\Action::make('Ellenőrzés')
                                                ->action(
                                                        function($livewire)
                                                        {
                                                            $data = $livewire->form->getState();
                                                            $checkable_coupon_code = $data['coupon_code'];
                                                            if (!empty($checkable_coupon_code))
                                                            {
                                                                $finding_match = Coupon::where('coupon_code', $checkable_coupon_code)->first();
                                                                if (!empty($finding_match))
                                                                {
                                                                    //Ez fut le ha már van ilyen kupon a táblában
                                                                }
                                                                if (empty($finding_match))
                                                                {
                                                                    $response_coupon = Http::withBasicAuth(env('BALLONOZZ_API_USER_KEY'), env('BALLONOZZ_API_SECRET_KEY'))->get('https://ballonozz.hu/wp-json/wc/v3/orders/'.$checkable_coupon_code);
                                                                    //Felőtt(3db->3f): 1567
                                                                    //Családi(1db->2f+2gy): 1508
                                                                    if ($response_coupon->successful())
                                                                    {
                                                                        $coupons_data = $response_coupon->json();
                                                                        dd($payment_status = ($coupons_data['status']));
                                                                        foreach($coupons_data['line_items'] as $coupon)
                                                                        {
                                                                            $response_item_nums = $coupon['quantity'];
                                                                            $response_product_id = $coupon['product_id'];

                                                                            $response_product_attributes = Http::withBasicAuth(env('BALLONOZZ_API_USER_KEY'), env('BALLONOZZ_API_SECRET_KEY'))->get('https://ballonozz.hu/wp-json/wc/v3/products/'.$response_product_id);
                                                                            if ($response_product_attributes->successful())
                                                                            {
                                                                                $product_attributes = $response_product_attributes->json();
                                                                                $tickettype = ($product_attributes['attributes'][0]['options'][0])*1;
                                                                                $adult = ($product_attributes['attributes'][1]['options'][0])*$response_item_nums;
                                                                                $children = ($product_attributes['attributes'][2]['options'][0])*$response_item_nums;

                                                                                $new_coupon = Coupon::create([
                                                                                    'user_id' => Auth::id(),
                                                                                    'coupon_code' => $checkable_coupon_code,
                                                                                    'source' => 'Ballonozz',
                                                                                    'adult' => $adult,
                                                                                    'children' => $children,
                                                                                    'tickettype_id' => $tickettype,
                                                                                    'status' => CouponStatus::CanBeUsed,
                                                                                ]);

                                                                                return redirect()->route('filament.admin.resources.coupons.edit', ['record' => $new_coupon]);

                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    )
                                                    ])
                                                    ->hidden(fn (GET $get, $operation): bool => ($get('source')=='Egyéb' || $operation=='edit')),*/

                                            /*
                                            Actions::make([Forms\Components\Actions\Action::make('Létrehozás')
                                                    //->hiddenOn('edit')
                                                    ->extraAttributes(['type'=>'submit'])
                                                    ->action(
                                                        function(Get $get, $livewire)
                                                        {
                                                            $data = $livewire->form->getState();
                                                            dd($data);
                                                            $checkable_coupon_code = $get('coupon_code');
                                                            $adult = $get('adult');
                                                            $children = $get('children');
                                                            if (!empty($checkable_coupon_code) && !empty($adult))
                                                            {
                                                                $new_coupon = Coupon::create([
                                                                'user_id' => Auth::id(),
                                                                'coupon_code' => $checkable_coupon_code,
                                                                'source' => 'Egyéb',
                                                                'adult' => $adult,
                                                                'children' => $children,
                                                                'tickettype_id' => NULL,
                                                                'status' => CouponStatus::UnderProcess,
                                                                ]);
                                                                return redirect()->route('filament.admin.resources.coupons.edit', ['record' => $new_coupon]);
                                                            }
                                                        })
                                                    ])
                                                    ->hidden(fn (GET $get, $operation): bool => ($get('source')!='Egyéb' || $operation=='edit')),
                                                    */
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
                            ->hidden(fn (GET $get, $operation): bool => ($get('source')!='Egyéb' && $operation=='create'))
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
                                            ->default(function (){
                                                return Carbon::today()->addDay();
                                            })
                                            ->minDate(function (){
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
                                /*
                                Fieldset::make('Extra beállítások')
                                    ->schema([
                                        Forms\Components\Toggle::make('vip')
                                            ->inline(false)
                                            ->onColor('success')
                                            ->onIcon('tabler-check')
                                            ->offIcon('tabler-x')
                                            ->helperText('Kapcsold be amennyiben ez egy VIP kupon.')
                                            ->label('VIP')
                                            ->disabledOn('edit')
                                            ->default(0),
                                        Forms\Components\Toggle::make('private')
                                            ->inline(false)
                                            ->onColor('success')
                                            ->onIcon('tabler-check')
                                            ->offIcon('tabler-x')
                                            ->helperText('Kapcsold be amennyiben ez egy Privát kupon.')
                                            ->label('Privát')
                                            ->disabledOn('edit')
                                            ->default(0),

                                    ])->columns(2),
                                    */
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
                ->visible(fn (GET $get, $operation) => (($get('adult') + $get('children')) > 0) && $operation == 'edit')
                ->schema([
                    Section::make()
                    ->schema([
                        Repeater::make('passengers')
                        ->addActionLabel('Új utas felvétele')
                        ->label('Utasok')
                        ->relationship()
                        ->minItems(fn (Get $get) => $get('adult')+$get('children'))
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
                                ->maxLength(255),
                                DatePicker::make('date_of_birth')
                                ->label('Születési dátum')
                                ->prefixIcon('tabler-calendar')
                                ->weekStartsOnMonday()
                                ->displayFormat('Y-m-d'),
                                TextInput::make('id_card_number')
                                ->label('Igazolvány szám')
                                ->prefixIcon('tabler-id')
                                ->placeholder('432654XX')
                                ->minLength(3)
                                ->maxLength(10),
                                TextInput::make('body_weight')
                                ->label('Testsúly')
                                ->prefixIcon('iconoir-weight-alt')
                                ->numeric()
                                ->minLength(1)
                                ->maxLength(10)
                                ->suffix(' kg'),
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
                                ->content('Kérem adja meg elérhetőségeit, az esetleges, fontos kapcsolatfelvétel céljából. Az itt megadott adatait csak és kizárólag fontos, eseménybeni változásokkor használjuk.'),
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
                                ->mask('+36999999999')
                                ->maxLength(30)
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
            /*->heading('Clients')->description('ez egy teszt')
            ->striped()*/
            /*
            ->defaultSort('expiration_at', 'desc')
            ->defaultGroup('status')
            ->groups([
                Group::make('status')
                    ->label('Státusz')
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('expiration_at', $direction))
                    ->collapsible(),
            ])
            ->groupingSettingsHidden()
            ->recordClasses(fn (Model $record) => $record->expiration_at < now() ? 'opacity-[50%]' : null)
            */
            ->recordClasses(function(Coupon $record)
            {
                if ($record->parent_id != null)
                {
                    return 'bg-gray-300/70 dark:bg-gray-600/30';
                }

                return;
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
                ->tooltip(fn($state) => $state ? 'Hiányzó utasadatok!':''),
                IconColumn::make('childrenCoupons')
                ->label('')
                ->width(0)
                ->boolean()
                ->trueIcon('tabler-ticket')
                ->size(IconColumn\IconColumnSize::Large)
                ->trueColor('warning')
                ->falseIcon('')
                ->tooltip(fn($state, $record) => $state ? 'Összevonva az alábbi kupon(ok)al: '.implode(', ', $state->pluck('coupon_code')->toArray()):''),
                TextColumn::make('coupon_code')
                ->label('Kupon azonosító 1')
                //->description(fn (Coupon $record): string => $record->source)
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
                    return'<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$payload->adult.'</span><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;"> felnőtt</span> <span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$payload->children.'</span><span class="text-gray-500 dark:text-gray-400" style="font-size:9pt;"> gyerek</span></p>';
                })->html()
                ->searchable()
                ->visibleFrom('md'),
                TextColumn::make('expiration_at')
                ->label('Lejárat')
                /*
                ->formatStateUsing(function($state)
                {
                    $diff_day_nums = Carbon::parse($state)->diffInDays('now', false);
                    return abs($diff_day_nums).($diff_day_nums < 0 ? ' nap múlva lejár' : ' napja lejárt');
                })
                ->description(function($state)
                {
                    return Carbon::parse($state)->translatedFormat('Y F d');
                })
                */
                ->formatStateUsing(function($state)
                {
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
                //Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Megtekintés')->link()
                //->hidden(fn ($record) => ($record->status==CouponStatus::Used)),
                //Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link()
                //->hidden(fn ($record) => ($record->status==CouponStatus::Used)),
                Tables\Actions\DeleteAction::make()->label(false)->tooltip('Törlés')
                ->hidden(fn ($record) => ($record->status==CouponStatus::Used || $record->status==CouponStatus::Expired || $record->status==CouponStatus::Applicant || $record->parent_id != null)),
            ])
            ->headerActions([
                /*
                \Filament\Actions\Action::getCreateFormAction()
                    ->visible(fn (GET $get, $operation) => ($get('source') == 'Egyéb') && $operation == 'create'),*/
            ])
            ->recordUrl(
                /* így is lehet
                fn (Coupon $record): string => ($record->status==CouponStatus::Used) ?false: route('filament.admin.resources.coupons.edit', ['record' => $record]),
                vagy úgy ahogy ez alatt van */
                function($record)
                {
                    if ($record->status==CouponStatus::Used || $record->status==CouponStatus::Expired || $record->status==CouponStatus::Applicant || $record->parent_id != null)
                    {
                        return false;
                    }
                    else
                    {
                        return route('filament.admin.resources.coupons.edit', ['record' => $record]);
                    }
                },
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Mind törlése'),
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
