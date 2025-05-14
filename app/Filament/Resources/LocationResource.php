<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\AreaType;
use App\Models\Location;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
/* saját use-ok */
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'iconoir-strategy';

    protected static ?string $modelLabel = 'helyszín';

    protected static ?string $pluralModelLabel = 'helyszínek';

    protected static ?string $navigationGroup = 'Alapadatok';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                    ->helperText('Adj egy fantázianevet a helyszínnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott helyszín.')
                                    ->label('Elnevezés')
                                    ->prefixIcon('tabler-writing-sign')
                                    ->placeholder('Békés Airport')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                                'xl' => 6,
                                '2xl' => 4,
                            ]),

                        Section::make()
                            ->schema([
                                Select::make('region_id')
                                    ->label('Régió')
                                    ->helperText('Válaszd ki vagy a "+" gombra kattintva, hozz létre egy új régiót, ahova tartozik az adott helyszín.')
                                    ->prefixIcon('iconoir-strategy')
                                    ->preload()
                                    //->options(Region::all()->pluck('name', 'id'))
                                    ->relationship(name: 'region', titleAttribute: 'name')
                                    ->native(false)
                                    ->required()
                                    ->searchable()
                                    ->createOptionForm([
                                        TextInput::make('name')->label('Régió neve')->helperText('Add meg az új régió nevét. Célszerű olyat választani ami a későbbiekben segíthet a könnyebb azonosítás tekintetében.')
                                            ->required()->unique(), ]),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                                'xl' => 6,
                                '2xl' => 4,
                            ]),
                    ]),

                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->schema([
                                Fieldset::make('Település')
                                    ->schema([
                                        TextInput::make('zip_code')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                            /*->helperText('Adj egy fantázianevet a helyszínnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott helyszín.')*/
                                            ->label('Irányítószám')
                                            ->prefixIcon('tabler-mailbox')
                                            ->placeholder('5600'),
                                        TextInput::make('settlement')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            /*->helperText('Ide a légijármű lajstromjelét add meg.')*/
                                            ->label('Település')
                                            ->prefixIcon('tabler-building-skyscraper')
                                            ->placeholder('Békéscsaba'),
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 3,
                                        '2xl' => 3,
                                    ]),

                                Fieldset::make('Cím')
                                    ->schema([
                                        TextInput::make('address')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            /*->helperText('Ide a légijármű lajstromjelét add meg.')*/
                                            ->label('Cím')
                                            ->prefixIcon('tabler-map-pin')
                                            ->placeholder('Repülőtér'),
                                        /*
                            Select::make('area_type_id')
                                ->label('Típus')
                                ->prefixIcon('tabler-layout-list')
                                ->options(AreaType::all()->pluck('name', 'id'))
                                ->searchable()
                                ->native(false),
                            TextInput::make('address_number')
                                ->label('Házszám')
                                ->prefixIcon('tabler-number')
                                ->numeric()
                                ->placeholder('13'),
                                */
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                        'xl' => 1,
                                        '2xl' => 1,
                                    ]),

                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 7,
                                'xl' => 8,
                                '2xl' => 7,
                            ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('Helyszín')
                                    ->schema([
                                        TextInput::make('coordinates')
                                            ->helperText('Megadhatod a helyszín szélességi és hosszúsági koordinátáit.')
                                            ->label('Koordináták')
                                            ->prefixIcon('tabler-compass')
                                            ->placeholder('47.6458345, 19.9761906'),

                                        TextInput::make('online_map_link')
                                            ->helperText('Megadhatod a helyszín, térkép linkjét a könnyebb útvonaltervezés céljából.')
                                            ->label('Online térkép link')
                                            ->prefixIcon('tabler-map-route')
                                            ->placeholder('https://www.google.com/maps?q=47.6458345,19.9761906')
                                            ->live(),

                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('Megttekintés térképen')
                                                ->icon('tabler-arrow-loop-right')
                                                ->visible(function ($record) {
                                                    if (! empty($record->coordinates)) {
                                                        return true;
                                                    }
                                                })
                                                ->url(function ($record) {
                                                    return 'http://maps.google.com/maps?q='.$record->coordinates;
                                                })
                                                ->openUrlInNewTab(),
                                            /*
                                ->hidden(fn (GET $get, $operation): bool => ($operation=='create'))
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $set('excerpt', str($get('content'))->words(45, end: ''));
                                    })*/
                                        ]),

                                        FileUpload::make('image_path')
                                            ->label('Kép feltöltése')
                                            ->helperText('Feltölthetsz fényképet a helyszínről, hogy az könnyebben beazonosítható legyen.')
                                            ->directory('form-attachments')
                                            ->image()
                                            ->maxSize(10000),
                                    ])->columns([
                                        'sm' => 2,
                                        'md' => 2,
                                        'lg' => 1,
                                        'xl' => 1,
                                        '2xl' => 1,
                                    ]),
                            ])->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 5,
                                'xl' => 4,
                                '2xl' => 5,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('region.name')
                    ->label('Régió'),

                TextColumn::make('name')
                    ->label('Elnevezés')
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Cím')
                    ->formatStateUsing(function ($state, Location $location) {
                        $areatype_name = AreaType::find($location->area_type_id);

                        //return $location->zip_code . ' ' . $location->settlement . ', '. $location->address . ' ' . $areatype_name->name . ' ' . $location->address_number .'.';
                        return $location->zip_code.' '.$location->settlement.', '.$location->address.'.';
                    })->visibleFrom('md'),

                TextColumn::make('coordinates')
                    ->label('Koordináták')
                    ->formatStateUsing(function ($state) {
                        [$latitude, $longitude] = explode(',', $state);

                        return '<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$latitude.'</span>, <span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$longitude.'</span></p>';
                    })
                    ->html()
                    ->icon('tabler-compass'),
                TextColumn::make('online_map_link')
                    ->icon('tabler-map-route')
                    ->formatStateUsing(function ($state) {
                        $wrapText = '...';
                        $count = 40;
                        if (strlen($state) > $count) {
                            preg_match('/^.{0,'.$count.'}(?:.*?)\b/siu', $state, $matches);
                            $text = $matches[0];
                        } else {
                            $wrapText = '';
                        }

                        return $text.$wrapText;
                    }),
                ImageColumn::make('image_path')
                    ->label('Kép')
                    ->square(),
            ])
            ->filters([
                TrashedFilter::make()->native(false),
            ])
            ->actions([
                /*
                Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Megtekintés')->link(),
                Tables\Actions\Action::make('delete')->icon('heroicon-m-trash')->color('danger')->hiddenLabel()->tooltip('Törlés')->link()->requiresConfirmation()->action(fn ($record) => $record->delete()),
                */
                Action::make('coordinates')
                    ->icon('tabler-arrow-loop-right')
                    ->hiddenLabel()
                    ->tooltip('Ide kattintva megtekintheti egy új ablakban a helyszínt a térképen.')
                    ->url(function ($record) {
                        //return 'https://www.google.com/maps/@'.$record->latitude.','.$record->longitude.','.$record->map_zoom.'z?entry=ttu';
                        return 'http://maps.google.com/maps?q='.$record->coordinates;
                    })
                    ->openUrlInNewTab()
                    ->visible(function ($record) {
                        if (! empty($record->coordinates)) {
                            return true;
                        }
                    }),
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            /*'view' => Pages\ViewLocation::route('/{record}'),*/
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi helyszín van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
