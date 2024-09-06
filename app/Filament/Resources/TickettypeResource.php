<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TickettypeResource\Pages;
use App\Models\Tickettype;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
/* saját use-ok */
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TickettypeResource extends Resource
{
    protected static ?string $model = Tickettype::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'jegytípus';

    protected static ?string $pluralModelLabel = 'jegytípusok';

    protected static ?string $navigationGroup = 'Alapadatok';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(6)
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                    ->helperText('Adj egy fantázianevet a jegytípusnak. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz.')
                                    ->label('Megnevezés')
                                    ->prefixIcon('tabler-writing-sign')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255),
                                Textarea::make('description')
                                    /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                    ->rows(4)
                                    ->cols(20)
                                    ->autosize()
                                    ->helperText('Itt néhány sorban leírhatod ennek a jegytípusnak a jellemzőit.')
                                    ->label('Leírás'),
                            ])->columnSpan([
                            'sm' => 6,
                            'md' => 6,
                            'lg' => 3,
                            'xl' => 2,
                            '2xl' => 2,
                        ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('Jegytípus paraméterek')
                                    ->schema([
                                        ColorPicker::make('color')
                                            ->helperText('Válassz egy egyedi színt a jegytípusnak, a könnyebb megkülömböztetés érdekében.')
                                            ->label('Jegytípus színe')
                                            ->prefixIcon('tabler-color-swatch'),

                                        ToggleButtons::make('aircrafttype')
                                            ->helperText('Válaszd ki a légijármű típusát.')
                                            ->label('Légijármű típusa')
                                            ->inline()
                                            /*->grouped()*/
                                            ->required()
                                            ->live()
                                            ->options([
                                                '0' => 'Hőlégballon',
                                                '1' => 'Kisrepülő',
                                            ])
                                            ->icons([
                                                '0' => 'iconoir-hot-air-balloon',
                                                '1' => 'iconoir-airplane',
                                            ])
                                            ->colors([
                                                '0' => 'info',
                                                '1' => 'info',
                                            ])
                                            ->default(0),

                                    ])->columns(1),
                                Fieldset::make('Repülhető régiók')
                                    ->schema([
                                        Select::make('regions')
                                            ->label('')
                                            ->relationship(titleAttribute: 'name')
                                            ->loadingMessage('Régiók betöltése betöltése...')
                                            /*
                                        ->suffixAction(function () {
                                            return Action::make('remove_all')
                                                ->icon('heroicon-s-x-circle')
                                                ->tooltip('Összes törlése')
                                                ->action(fn ($set) => $set('regions', null));
                                        })
                                        */
                                            ->multiple()
                                            ->preload(),
                                    ])->columns(1),
                                /*
                            Forms\Components\Fieldset::make('Társított légijárművek')
                                ->schema([
                                    Forms\Components\Select::make('aircrafts')
                                        ->label(false)
                                        ->helperText('Itt rögzíthetsz több légijárművet az adott jegytípushoz.')
                                        ->multiple()
                                        ->relationship(titleAttribute: 'name')
                                        ->preload(),
                                        /*
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required()->unique(),]),
                                                */
                                /*
                                ])->columns(1),*/

                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 3,
                                'xl' => 2,
                                '2xl' => 2,
                            ]),

                        Section::make()
                        //->hidden(fn (GET $get, $operation): bool => ($get('default')== 1 && $operation=='edit'))
                            ->schema([
                                Placeholder::make('default_placeholder')
                                    ->label('Alapértelmezett'),
                                Toggle::make('default')
                                    ->onColor('success')
                                    ->onIcon('tabler-check')
                                    ->offIcon('tabler-x')
                                    ->helperText('Amennyiben ezt bekapcsolod, abban az esetben ez a jegytípus lesz az, amit alapértelmezettként használ későbbiekben a rendszer abba az esegtben, ha úgy veszel fel légijárművet, hogy annak nem adsz meg jegytípust.')
                                    ->label('Beállítás alapértelmezettként')
                                    //->disabled(fn (GET $get): bool => ($get('default')=='1'))
                                    ->default(0),
                            ])->columnSpan([
                            'sm' => 6,
                            'md' => 6,
                            'lg' => 6,
                            'xl' => 2,
                            '2xl' => 2,
                        ]),
                        /*
                    Section::make()
                    ->schema([
                            Forms\Components\Fieldset::make('Utasok száma')
                            ->schema([
                                Forms\Components\TextInput::make('adult')
                                ->helperText('Add meg a jegytípushoz tartozó felnőtt utasok számát.')
                                ->label('Felnőtt')
                                ->prefixIcon('tabler-friends')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->minLength(1)
                                ->maxLength(2)
                                ->suffix(' fő'),

                                Forms\Components\TextInput::make('children')
                                ->helperText('Add meg a jegytípushoz tartozó gyermek utasok számát.')
                                ->label('Gyerek')
                                ->prefixIcon('tabler-horse-toy')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->minLength(1)
                                ->maxLength(2)
                                ->suffix(' fő'),

                            ])->columns(2),

                            Forms\Components\Fieldset::make('Extra beállítások')
                            ->schema([
                            Forms\Components\Toggle::make('vip')
                                ->inline(false)
                                ->onColor('success')
                                ->onIcon('tabler-check')
                                ->offIcon('tabler-x')
                                ->helperText('Kapcsold be amennyiben ez egy VIP jegytípus.')
                                ->label('VIP')
                                ->default(0),
                            Forms\Components\Toggle::make('private')
                                ->inline(false)
                                ->onColor('success')
                                ->onIcon('tabler-check')
                                ->offIcon('tabler-x')
                                ->helperText('Kapcsold be amennyiben ez egy Privát jegytípus.')
                                ->label('Privát')
                                ->default(0),

                            ])->columns(2),

                        ])->columnSpan(2),
                    */
                        /*
                        Section::make()
                        ->schema([
                            Forms\Components\Fieldset::make('Forrás beállítások')
                            ->schema([
                                Forms\Components\TextInput::make('source')
                                ->helperText('itt rögzítheted, hogy melyik szolgáltatótól érkezik az erre a jegyre vonatkozó hivatkozás. pl.: Meglepkék')
                                ->label('Forrás')
                                ->prefixIcon('tabler-writing-sign')
                                ->required()
                                ->minLength(3)
                                ->maxLength(255),
                            Forms\Components\TextInput::make('name_stored_at_source')
                                ->helperText('Rögzítsd, hogy a forrásnál milyen néven szerepel az adott jegytípus.')
                                ->label('Forrásnál tárol megnevezés')
                                ->prefixIcon('tabler-writing-sign')
                                ->required()
                                ->minLength(3)
                                ->maxLength(255),
                                ])->columns(1),

                            Forms\Components\Fieldset::make('Társított légijárművek')
                                ->schema([
                                    Forms\Components\Select::make('aircrafts')
                                        ->label(false)
                                        ->helperText('Itt rögzíthetsz több légijárművet az adott jegytípushoz.')
                                        ->multiple()
                                        ->relationship(titleAttribute: 'name')
                                        ->preload(),
                                        /*
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required()->unique(),]),
                                                */
                        /*
                                ])->columns(1),

                            ])->columnSpan(2),*/
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Attribútum ID')
                    ->tooltip('Amennyiben WooCommerce, vagy hasonló webshopot üzemeltet, az adott termékhez hozzá kell adni - mint rejtett attribútomot - "tickettype" néven úgy, hogy az attribútum értékének ezt az értéket kell megadni. Mindemellett további két fontos rejtett attribútumot is elengedhetetlen a működéshez, amelyek: a felnőtt utasok száma, azaz "adult", és ennek értéke, valamint a gyermek utasok száma, azaz "children", és ennek értéke.')
                    ->visibleFrom('sm'),

                TextColumn::make('name')
                    ->label('Megnevezés')
                    //->description(fn (Tickettype $record): string => $record->description)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Megjegyzés')
                    //->description(fn (Tickettype $record): string => $record->description)
                    ->wrap()
                    ->searchable(),

                TextColumn::make('aircrafttype')
                    ->label('Típus')
                    ->badge()
                    ->size('md'),

                ColorColumn::make('color')
                    ->label('Szín')
                    ->visibleFrom('md'),

                IconColumn::make('default')
                    ->label('Alapértelmezett')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => '',
                        '1' => 'tabler-circle-check',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => '',
                        '1' => 'success',
                    })
                    ->visibleFrom('sm'),

                /*
            Tables\Columns\TextColumn::make('source')
                ->label('Forrás')
                ->description(fn (Tickettype $record): string => $record->name_stored_at_source)
                ->wrap()
                ->searchable(),
            Tables\Columns\TextColumn::make('adult')
                ->label('Utasok')
                ->formatStateUsing(function ($state, Tickettype $payload) {
                    return '<p style="color:gray; font-size:9pt;"><b style="color:white; font-size:11pt; font-weight:normal;">'.$payload->adult . '</b> felnőtt</p><p style="color:gray; font-size:9pt;"><b style="color:white; font-size:11pt; font-weight:normal;">' . $payload->children . '</b> gyerek</p>';
                })->html()
                ->searchable(),

            Tables\Columns\TextColumn::make('vip')
                ->label(false)
                ->badge()
                ->size('sm'),

            Tables\Columns\TextColumn::make('private')
                ->label(false)
                ->badge()
                ->size('sm'),

            Tables\Columns\TextColumn::make('aircrafts.name')
                ->label('Társított légijárművek')
                ->searchable()
                ->wrap()
                ->badge()
                ->size('sm'),
                */
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()->native(false),
            ])
            ->actions([
                /*
                Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Megtekintés')->link(),
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
                Tables\Actions\Action::make('delete')->icon('heroicon-m-trash')->color('danger')->hiddenLabel()->tooltip('Törlés')->link()->requiresConfirmation()->action(fn ($record) => $record->delete()),
                */
                Tables\Actions\DeleteAction::make()->label(false)->tooltip('Törlés'),
                Tables\Actions\ForceDeleteAction::make()->label(false)->tooltip('Végleges törlés'),
                Tables\Actions\RestoreAction::make()->label(false)->tooltip('Helyreállítás'),
            ])
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
            'index' => Pages\ListTickettypes::route('/'),
            'create' => Pages\CreateTickettype::route('/create'),
            'edit' => Pages\EditTickettype::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi jegytípus van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
