<?php

namespace App\Filament\Resources;

use App\Enums\AircraftLocationPilotStatus;
use App\Filament\Forms\Components\CustomDatePicker;
use App\Filament\Resources\AircraftLocationPilotResource\Pages;
use App\Filament\Resources\AircraftLocationPilotResource\Pages\ListCheckins;
use App\Models\Aircraft;
use App\Models\AircraftLocationPilot;
use App\Models\Location;
use App\Models\Pilot;
use App\Models\Region;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AircraftLocationPilotResource extends Resource
{
    protected static ?string $model = AircraftLocationPilot::class;

    protected static ?string $navigationIcon = 'iconoir-database-script';

    protected static ?string $modelLabel = 'repülési terv';

    protected static ?string $pluralModelLabel = 'repülési tervek';

    protected static ?string $navigationGroup = 'Alapadatok';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->schema([
                                Fieldset::make('Tervezett repülés ideje')
                                    ->schema([
                                        CustomDatePicker::make('date')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                            ->label('Dátum')
                                            ->prefixIcon('tabler-calendar')
                                            ->weekStartsOnMonday()
                                            ->format('Y-m-d')
                                            ->required(),

                                        TimePicker::make('time')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            ->label('Időpont')
                                            ->prefixIcon('tabler-clock')
                                            //->placeholder(now())
                                            ->displayFormat('H:i:s')
                                            ->required(),

                                        Select::make('period_of_time')
                                            ->label('Program tervezett időtartama')
                                            ->options([
                                                '00:30:00' => 'fél óra',
                                                '01:00:00' => '1 óra',
                                                '01:30:00' => '1 és fél óra',
                                                '02:00:00' => '2 óra',
                                                '02:30:00' => '2 és fél óra',
                                                '03:00:00' => '3 óra',
                                                '03:30:00' => '3 és fél óra',
                                                '04:00:00' => '4 óra',
                                                '04:30:00' => '4 és fél óra',
                                                '05:00:00' => '5 óra',
                                                '05:30:00' => '5 és fél óra',
                                                '06:00:00' => '6 óra',
                                            ])
                                            ->prefixIcon('tabler-device-watch-check')
                                            ->preload()
                                            ->required()
                                            ->native(false),
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 2,
                                        '2xl' => 3,
                                    ]),

                                Fieldset::make('Tervezett repülés paraméterei')
                                    ->schema([
                                        Select::make('aircraft_id')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            ->label('Légijármű')
                                            ->prefixIcon('tabler-ufo')
                                            ->relationship('aircraft', 'name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "({$record->registration_number}) {$record->name}")
                                            ->preload()
                                            ->native(false)
                                            ->required()
                                            ->searchable(),

                                        Select::make('region_id')
                                            ->label('Régió')
                                            ->prefixIcon('tabler-map-route')
                                            ->options(Region::all()->pluck('name', 'id'))
                                            ->native(false)
                                            ->required()
                                            ->searchable(),
                                        /*
                                Select::make('location_id')
                                    ->label('Helyszín')
                                    ->prefixIcon('iconoir-strategy')
                                    ->options(Location::all()->pluck('name', 'id'))
                                    ->native(false)
                                    //->required()
                                    //->disabled()
                                    ->searchable(),
                                    */
                                        Select::make('pilot_id')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            ->label('Pilóta')
                                            ->prefixIcon('iconoir-user-square')
                                            ->options(Pilot::all()->pluck('fullname', 'id')) // <-ez egy modell szinten deklarált atribútum
                                            ->native(false)
                                            ->searchable(),
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 2,
                                        '2xl' => 3,
                                    ]),

                            ])->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 8,
                                '2xl' => 8,
                            ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('Státusz')
                                    ->schema([
                                        ToggleButtons::make('status')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Válassza ki a légijármű típusát.')*/
                                            ->helperText('A repülés terv státuszával megjelölheted az adott repülés állapotát.')
                                            ->label('Repülési terv státusza')
                                            ->inline()
                                            /*->grouped()*/
                                            ->required()
                                            ->options(function ($state) {
                                                if ($state == 3) {
                                                    return [
                                                        '3' => 'Végrehajtott',
                                                        '5' => 'Visszajelzés',
                                                    ];
                                                }
                                                if ($state != 3) {
                                                    return [
                                                        '0' => 'Tervezett',
                                                        '1' => 'Publikált',
                                                        '2' => 'Véglegesített',
                                                        '3' => 'Végrehajtott',
                                                        '4' => 'Törölt',
                                                    ];
                                                }
                                            })
                                            ->colors([
                                                '0' => 'warning',
                                                '1' => 'success',
                                                '2' => 'success',
                                                '3' => 'info',
                                                '4' => 'danger',
                                                '5' => 'info',
                                            ])
                                            ->icons([
                                                '0' => 'tabler-player-pause',
                                                '1' => 'tabler-player-play',
                                                '2' => 'tabler-flag-check',
                                                '3' => 'tabler-player-stop',
                                                '4' => 'tabler-playstation-x',
                                                '5' => 'tabler-mail-heart',
                                            ])
                                            //->disabled(fn ($record) => $record && in_array($record->status, [AircraftLocationPilotStatus::Executed, AircraftLocationPilotStatus::Deleted]))
                                            ->disabled(fn ($record) => $record && in_array($record->status, [AircraftLocationPilotStatus::Deleted]))
                                            ->default(0),
                                    ])->columns(1),
                            ])->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 4,
                                '2xl' => 4,
                            ]),

                    ]),

                Grid::make(12)
                    ->schema([
                        Section::make()
                            ->schema([
                                Fieldset::make('Publikus leírás')
                                    ->schema([
                                        Textarea::make('public_description')
                                            ->label('')
                                            ->helperText('Adj egy rövid leírást a légijárműhöz. Az ide rögzített leírás megjeleník a Repülési tervek/Jeletkezők részben.')
                                            ->rows(4)
                                            ->cols(20),
                                    ])->columns(1),
                            ])->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 6,
                                '2xl' => 6,
                            ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('NEM publikus leírás')
                                    ->schema([
                                        Textarea::make('non_public_description')
                                            ->label('')
                                            ->helperText('Adj egy rövid leírást a légijárműhöz. Az ide rögzített leírás megjeleník a Repülési tervek/Jeletkezők részben.')
                                            ->rows(4)
                                            ->cols(20),
                                    ])->columns(1),
                            ])->columnSpan([
                                'sm' => 12,
                                'md' => 12,
                                'lg' => 12,
                                'xl' => 6,
                                '2xl' => 6,
                            ]),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Group::make('date')
                    ->getTitleFromRecordUsing(fn ($record) => $record->date->format('Y.m.d.'))
                    ->getTitleFromRecordUsing(fn ($record) => Carbon::parse($record->date)->translatedFormat('Y.m.d.'))
                    ->orderQueryUsing(fn (Builder $query) => $query->orderBy('date', 'desc'))
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            )
            ->columns([
                TextColumn::make('id')
                    ->label(false)
                    ->icon('tabler-number')
                    ->badge()
                    ->color('gray')
                    ->size('md')
                    ->visibleFrom('md'),
                TextColumn::make('status')
                    ->label(false)
                    ->badge()
                    ->size('md'),

                TextColumn::make('time')
                    ->label('Időpont')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('H:i'))
                    ->searchable(),
                TextColumn::make('region.name')
                    ->label('Régió')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->label('Helyszín')
                    ->searchable(),
                TextColumn::make('aircraft.name')
                    ->label('Légijármű')
                    ->searchable(['aircraft.registration_number', 'aircraft.name'])
                    ->formatStateUsing(fn ($record) => "({$record->aircraft->registration_number}) {$record->aircraft->name}")
                    ->visibleFrom('md'),
                TextColumn::make('pilot.fullname')
                    ->label('Pilóta')
                    ->searchable(['pilots.lastname', 'pilots.firstname']),

            ])
            ->filters([
                SelectFilter::make('aircraft_id')
                    ->label('Légijármű')
                    ->relationship('aircraft', 'name')
                    ->native(false),
                SelectFilter::make('region_id')
                    ->label('Régió')
                    ->relationship('region', 'name')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('checkins')
                    ->label('')
                    ->icon('tabler-users-group')
                    ->tooltip(ListCheckins::getNavigationLabel())
                    ->badge(fn ($record) => $record->coupons->sum('membersCount'))
                    ->hidden(fn ($record) => ! $record->coupons->count())
                    ->action(fn ($record) => redirect(route('filament.admin.resources.aircraft-location-pilots.checkins', $record->id))),
                /*
                ViewAction::make()->hiddenLabel()->tooltip('Megtekintés')->link(),
                EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
                Tables\Actions\Action::make('delete')->icon('heroicon-m-trash')->color('danger')->hiddenLabel()->tooltip('Törlés')->link()->requiresConfirmation()->action(fn ($record) => $record->delete()),
                */
                DeleteAction::make()
                    ->hidden(fn ($record) => (bool) $record->coupons->count())
                    ->label(false)
                    ->tooltip('Törlés'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Mind törlése')
                        ->deselectRecordsAfterCompletion(),
                ])->label('Csoportos bejegyzés műveletek'),

                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('status_change_draft')
                        ->label('Tervezett')
                        ->icon('tabler-player-pause')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = AircraftLocationPilotStatus::Draft;
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('status_change_published')
                        ->label('Publikált')
                        ->icon('tabler-player-play')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = AircraftLocationPilotStatus::Published;
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('status_change_finalized')
                        ->label('Véglegesített')
                        ->icon('tabler-flag-check')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = AircraftLocationPilotStatus::Finalized;
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('status_change_executed')
                        ->label('Végrehajtott')
                        ->icon('tabler-player-stop')
                        ->color('info')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = AircraftLocationPilotStatus::Executed;
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('status_change_deleted')
                        ->label('Törölt')
                        ->icon('tabler-playstation-x')
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->status = AircraftLocationPilotStatus::Deleted;
                                $record->save();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ])->label('Csoportos repülési státusz műveletek'),

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
            'index' => Pages\ListAircraftLocationPilots::route('/'),
            'create' => Pages\CreateAircraftLocationPilot::route('/create'),
            /*'view' => Pages\ViewAircraftLocationPilot::route('/{record}'),*/
            'edit' => Pages\EditAircraftLocationPilot::route('/{record}/edit'),
            'checkins' => Pages\ListCheckins::route('/{record}/checkins'),
            'calendar' => Pages\Calendar::route('/calendar'),
        ];
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi publikált repülési terv van
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::where('status', '1')->count();
    }
}
