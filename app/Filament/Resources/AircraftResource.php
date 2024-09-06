<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AircraftResource\Pages;
use App\Models\Aircraft;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
/* saját use-ok */
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AircraftResource extends Resource
{
    protected static ?string $model = Aircraft::class;

    protected static ?string $navigationIcon = 'iconoir-airplane-rotation';

    protected static ?string $modelLabel = 'légijármű';

    protected static ?string $pluralModelLabel = 'légijárművek';

    protected static ?string $navigationGroup = 'Alapadatok';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                    ->helperText('Adj egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')
                                    ->label('Megnevezés')
                                    ->prefixIcon('tabler-writing-sign')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->label('Leírás')
                                    ->helperText('Adj egy rövid leírást a légijárműhöz. Az ide rögzített leírás megjeleník a Repülési tervek/Jeletkezők részben.')
                                    ->rows(4)
                                    ->cols(20),
                                Fieldset::make('Besorolás')
                                    ->schema([
                                        ToggleButtons::make('type')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Válassza ki a légijármű típusát.')*/
                                            ->helperText('Válaszd ki a légijármű típusát.')
                                            ->label('Típus')
                                            ->inline()
                                            /*->grouped()*/
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('tickettypes', null))
                                            ->required()
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
                                        TextInput::make('registration_number')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            ->helperText('Ide a légijármű lajstromjelét add meg.')
                                            ->label('Lajstrom-jel')
                                            ->prefixIcon('tabler-license')
                                            ->placeholder('HA-1234 vagy HA-ABCD')
                                            ->required()
                                            ->minLength(3)
                                            ->maxLength(10),
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ]),
                            ])->columnSpan([
                                'sm' => 4,
                                'md' => 4,
                                'lg' => 4,
                                'xl' => 2,
                                '2xl' => 2,
                            ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('Terhelhetőség')
                                    ->schema([
                                        TextInput::make('number_of_person')
                                            ->helperText('Add meg a MAXIMÁLISAN szállítható személyek számát.')
                                            ->label('Szállítható személyek száma')
                                            ->prefixIcon('fluentui-people-team-24-o')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->minLength(1)
                                            ->maxLength(10)
                                            ->suffix(' fő'),

                                        TextInput::make('payload_capacity')
                                            ->helperText('Add meg a légijármű - a pilótával együttes - MAXIMÁLIS terhetőségét kg-ban.')
                                            ->label('Terhetőség')
                                            ->prefixIcon('tabler-weight')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->step(10)
                                            ->minLength(2)
                                            ->maxLength(10)
                                            ->suffix(' kg'),

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
                                Toggle::make('unlimited')
                                    ->inline(false)
                                    ->onColor('success')
                                    ->onIcon('tabler-check')
                                    ->offIcon('tabler-x')
                                    ->helperText('Amennyiben ez be van kapcsolva, erre a légijárműre korlátlan számú utas jelentkezhet.')
                                    ->label('Korlátlan utasszám')
                                    ->default(0)
                                    ->live()
                                    ->disabled(fn (GET $get): bool => ($get('vip')!='0') || ($get('private')!='0'))
                                    ->dehydrated(true),

                                Toggle::make('vip')
                                    ->inline(false)
                                    ->onColor('success')
                                    ->onIcon('tabler-check')
                                    ->offIcon('tabler-x')
                                    ->helperText('Kapcsold be amennyiben ez a légijármű VIP jegytípussal is használható.')
                                    ->label('VIP')
                                    ->default(0)
                                    ->live()
                                    ->disabled(fn (GET $get): bool => ($get('unlimited')!='0'))
                                    ->dehydrated(true),

                                Toggle::make('private')
                                    ->inline(false)
                                    ->onColor('success')
                                    ->onIcon('tabler-check')
                                    ->offIcon('tabler-x')
                                    ->helperText('Kapcsold be amennyiben ez a légijármű PRIVÁT jegytípussal is használható.')
                                    ->label('Privát')
                                    ->default(0)
                                    ->live()
                                    ->disabled(fn (GET $get): bool => ($get('unlimited')!='0'))
                                    ->dehydrated(true),

                            ])->columns(3),*/

                                Fieldset::make('Társított jegytípusok')
                                    ->schema([
                                        Select::make('tickettypes')
                                            ->label('Jegytípusok')
                                            ->relationship(titleAttribute: 'name', modifyQueryUsing: fn (Builder $query, $get) => $query->where('aircrafttype', $get('type')))
                                            ->loadingMessage('Jegytípusok betöltése...')
                                            ->suffixAction(function () {
                                                return Action::make('remove_all')
                                                    ->icon('heroicon-s-x-circle')
                                                    ->tooltip('Összes törlése')
                                                    ->action(fn ($set) => $set('tickettypes', null));
                                            })
                                            ->multiple()
                                            ->preload(),
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 2,
                                        '2xl' => 2,
                                    ]),

                            ])->columnSpan([
                                'sm' => 4,
                                'md' => 4,
                                'lg' => 4,
                                'xl' => 2,
                                '2xl' => 2,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('registration_number')
                    ->label('Lajtsrom-jel')
                    //->description(fn (Aircraft $record): string => $record->name)
                    ->searchable(['registration_number', 'name'])
                    ->visibleFrom('md'),

                TextColumn::make('name')
                    ->label('Megnevezés')
                    //->description(fn (Aircraft $record): string => $record->registration_number)
                    ->searchable(['name', 'registration_number']),
                TextColumn::make('type')
                    ->label('Típus')
                    ->badge()
                    ->size('md'),
                TextColumn::make('number_of_person')->label('Száll.szem.száma')
                    ->formatStateUsing(fn ($state) => $state.' fő')
                    ->visibleFrom('lg'),
                TextColumn::make('payload_capacity')->label('Max. terhelhetőség')
                    ->formatStateUsing(fn ($state) => $state.' kg')
                    ->visibleFrom('lg'),
                /*
                IconColumn::make('unlimited')
                    ->label('Extra')
                    ->tooltip('Korlátlan utasszám felvétele')
                    ->boolean()
                    ->trueIcon('tabler-infinity')
                    ->falseIcon('tabler-infinity-off')
                    //->falseIcon('tabler-infinity')
                    //->falseIcon('')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->width(20)
                    ->size(IconColumn\IconColumnSize::Medium),
                IconColumn::make('vip')
                    ->label('')
                    ->tooltip('VIP')
                    ->boolean()
                    ->trueIcon('tabler-vip')
                    //->falseIcon('tabler-vip')
                    ->falseIcon('')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->width(20)
                    ->size(IconColumn\IconColumnSize::Medium),
                IconColumn::make('private')
                    ->label('')
                    ->tooltip('PRIVATE')
                    ->boolean()
                    ->trueIcon('tabler-crown')
                    //->falseIcon('tabler-crown')
                    ->falseIcon('')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->size(IconColumn\IconColumnSize::Medium),
                */
            ])

            ->filters([
                Tables\Filters\Filter::make('type')
                    ->query(fn (Builder $query) => $query->where('type', true)),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Típus')
                    ->options([
                        '0' => 'Hőlégballon',
                        '1' => 'Kisrepülő',
                    ])
                    ->native(false),
                Tables\Filters\TrashedFilter::make()->native(false),
            ])
            ->actions([
                //Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Megtekintés')->link(),
                //Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
                /*
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
            'index' => Pages\ListAircraft::route('/'),
            'create' => Pages\CreateAircraft::route('/create'),
            /*'view' => Pages\ViewAircraft::route('/{record}'),*/
            'edit' => Pages\EditAircraft::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi légijármű van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
