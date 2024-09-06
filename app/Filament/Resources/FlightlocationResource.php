<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightlocationResource\Pages;
use App\Models\AreaType;
use App\Models\Flightlocation;
use App\Models\Region;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
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
            //->heading('Várható repülési helyszínek')
            ->description('Itt folyamatosan nyomon követheted a már kiírt, valamint a jövöben kiírásra kerülő repüléseink helyszíneit.')
            ->emptyStateHeading('Nincs megjeleníthető helyszín.')
            ->emptyStateDescription('Amint kiírásra kerülnek új lehetséges repülési helyszínek itt azonnal megtekintheted azokat, régiónkénti bontásban.')
            ->emptyStateIcon('iconoir-database-script')
            //->defaultSort('region.name', 'asc')
            ->defaultGroup(
                Group::make('region.name')
                    ->getTitleFromRecordUsing(function ($record) {
                        return 'Régió: '.Region::find($record->region_id)->name;
                    })
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Elnevezés')
                    ->searchable(),

                TextColumn::make('zip_code')
                    ->label('Cím')
                    ->formatStateUsing(function (Flightlocation $location) {
                        $areatype_name = AreaType::find($location->area_type_id);

                        /*
                    return ($location->zip_code ? $location->zip_code . ' ' : '') .
                    ($location->settlement ? $location->settlement . ', ' : '') .
                    ($location->address ? $location->address . ' ' : '') .
                    ($areatype_name?->name ? $areatype_name?->name . ' ' : '') .
                    ($location->address_number ? $location->address_number . '.' : '');
                    */
                        return ($location->zip_code ? $location->zip_code.' ' : '').
                        ($location->settlement ? $location->settlement.', ' : '').
                        ($location->address ? $location->address.' ' : '');
                    })
                    ->searchable(['zip_code', 'settlement']),

                TextColumn::make('coordinates')
                    ->label('Navigáció')
                    ->formatStateUsing(function ($state) {
                        [$latitude, $longitude] = explode(',', $state);

                        return '<p><span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$latitude.'</span>, <span class="text-custom-600 dark:text-custom-400" style="font-size:11pt;">'.$longitude.'</span></p>';
                    })
                    ->html()
                    ->icon('tabler-compass'),
                /*
                TextColumn::make('online_map_link')
                ->icon('tabler-map-route')
                ->url(function ($record) {
                    return 'http://maps.google.com/maps?q='.$record->coordinates;
                })
                ->openUrlInNewTab()
                ->visibleFrom('md'),
                */
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
                    ->square()
                    ->visibleFrom('md'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('redirect')
                    ->icon('tabler-arrow-loop-right')
                    ->hiddenLabel()
                    ->tooltip('Ide kattintva megtekintheted egy új ablakban a helyszínt a térképen.')
                    ->url(function ($record) {
                        return $record->online_map_link;
                    })
                    ->openUrlInNewTab()
                    ->visible(function ($record) {
                        if (! empty($record->online_map_link)) {
                            return true;
                        }
                    }),
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
