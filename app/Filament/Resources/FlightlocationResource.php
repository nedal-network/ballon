<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightlocationResource\Pages;
use App\Models\Flightlocation;
use App\Models\Region;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class FlightlocationResource extends Resource
{
    protected static ?string $model = Flightlocation::class;

    protected static ?string $navigationIcon = 'fluentui-whiteboard-24-o';

    protected static ?string $modelLabel = 'repülési helyszín';

    protected static ?string $navigationLabel = 'Repülési Helyszínek';

    protected static ?string $pluralModelLabel = 'Várható repülési helyszínek';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->description('Itt folyamatosan nyomon követheted a már kiírt, valamint a jövöben kiírásra kerülő repüléseink helyszíneit.')
            ->emptyStateHeading('Nincs megjeleníthető helyszín.')
            ->emptyStateDescription('Amint kiírásra kerülnek új lehetséges repülési helyszínek itt azonnal megtekintheted azokat, régiónkénti bontásban.')
            ->emptyStateIcon('iconoir-database-script')
            ->groupingSettingsHidden()
            ->defaultGroup(
                Group::make('region.name')
                    ->getTitleFromRecordUsing(function ($record) {
                        return Region::find($record->region_id)->name;
                    })
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            )
            ->columns([
                Split::make([
                    Stack::make([
                        ImageColumn::make('image_path')
                            ->sortable(false)
                            ->label('Kép')
                            ->square()
                            ->width(75)
                            ->height(75)
                            ->tooltip('Ide kattintva megtekintheted egy új ablakban a helyszínt a térképen.')
                            ->url(fn ($record) => $record->online_map_link, true),
                    ])->extraAttributes(['class' => 'flex w-min'], true),

                    Stack::make([
                        TextColumn::make('name')
                            ->label('Elnevezés')
                            ->sortable(false)
                            ->weight(FontWeight::SemiBold)
                            ->searchable()
                            ->url(fn ($record) => $record->online_map_link, true),

                        TextColumn::make('zip_code')
                            ->label('Cím')
                            ->sortable(false)
                            ->formatStateUsing(function (Flightlocation $location) {
                                $data = [];
                                $location->zip_code && $data[] = $location->zip_code.' '.$location->settlement;
                                $location->address && $data[] = $location->address;

                                return implode(', ', $data);
                            })
                            ->searchable(['zip_code', 'settlement']),
                    ]),

                    Stack::make([
                        TextColumn::make('coordinates')
                            ->label('Navigáció')
                            ->sortable(false)
                            ->formatStateUsing(function ($state) {
                                $coordinates = explode(',', $state);

                                return implode(', ', $coordinates);
                            })
                            ->width(1)
                            ->url(fn ($record) => $record->online_map_link, true)
                            ->icon('tabler-compass')
                            ->visibleFrom('md'),
                    ]),
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListFlightlocations::route('/'),
            //'create' => Pages\CreateFlightlocation::route('/create'),
            //'edit' => Pages\EditFlightlocation::route('/{record}/edit'),
        ];
    }
}
