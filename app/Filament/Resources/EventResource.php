<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'tabler-timeline-event-text';

    protected static ?string $modelLabel = 'egyéb esemény';

    protected static ?string $pluralModelLabel = 'egyéb események';

    protected static ?string $navigationGroup = 'Alapadatok';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(6)
                    ->schema([

                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('Add meg az esemény nevét. Érdemes olyat adni, amit később megkönnyíti az esemény keresését.')
                                    ->label('Esemény neve')
                                    ->prefixIcon('tabler-writing-sign')
                                    ->required()
                                    ->minLength(3)
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->rows(4)
                                    ->cols(20)
                                    ->autosize()
                                    ->helperText('Itt néhány sorban leírhatod ennek az eseménynek a jellemzőit.')
                                    ->label('Leírás'),
                            ])
                            ->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 3,
                                'xl' => 2,
                                '2xl' => 2,
                            ]),

                        Section::make()
                            ->schema([
                                Fieldset::make('Esemény ideje')
                                    ->schema([
                                        DatePicker::make('start_date')
                                            ->helperText('Válaszd ki az esemény kezdő dátumát.')
                                            ->label('Esemény kezdete')
                                            ->prefixIcon('tabler-calendar')
                                            ->weekStartsOnMonday()
                                            ->displayFormat('Y-m-d')
                                            ->required(),
                                        DatePicker::make('end_date')
                                            ->helperText('Válaszd ki az esemény záró dátumát.')
                                            ->label('Esemény vége')
                                            ->prefixIcon('tabler-calendar')
                                            ->weekStartsOnMonday()
                                            ->displayFormat('Y-m-d')
                                            ->required(),
                                    ])->columns(1),
                            ])
                            ->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 3,
                                'xl' => 2,
                                '2xl' => 2,
                            ]),

                        Section::make()
                            ->schema([
                                Placeholder::make('status_placeholder')
                                    ->label('Státusz'),
                                Toggle::make('status')
                                    ->onColor('success')
                                    ->onIcon('tabler-check')
                                    ->offIcon('tabler-x')
                                    ->helperText('Amennyiben ezt bekapcsolod, az esemény publikálásra kerül a naptárban.')
                                    ->label('Publikálva')
                                    ->default(0),
                            ])
                            ->columnSpan([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
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
                IconColumn::make('status')
                    ->label('Státusz')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'tabler-circle-x',
                        '1' => 'tabler-circle-check',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        '0' => 'Nem publikált',
                        '1' => 'Publikált',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'danger',
                        '1' => 'success',
                    })
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Esemény neve')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Ideje')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function ($state, $record) {
                        //dump($end_date = $record->end_date);
                        //$end_date = Carbon::parse($end_date)->translatedFormat('Y F d');
                        //return Carbon::parse($state)->translatedFormat('Y F d').' -> '.Carbon::parse($record->end_date)->translatedFormat('Y F d');
                        return Carbon::parse($state)->translatedFormat('Y.m.d.').' -> '.Carbon::parse($record->end_date)->translatedFormat('Y.m.d.');
                    })
                    ->size('md')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Leírás')
                    ->formatStateUsing(function ($state) {
                        $text = '';
                        $wrapText = '...';
                        $count = 60;
                        if (strlen($state) > $count) {
                            preg_match('/^.{0,'.$count.'}(?:.*?)\b/siu', $state, $matches);
                            $text = $matches[0];
                        } else {
                            $wrapText = '';
                        }

                        return $text.$wrapText;
                    })
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                Filter::make('status')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('status', true))

                    ->label('Publikálva')
                    ->default(0),
                Filter::make('events')
                    ->label('Események')
                    ->form([
                        DatePicker::make('event_start')
                            ->label('Esemény kezdete'),
                        DatePicker::make('event_end')
                            ->label('Esemény vége'),
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('Created from '.Carbon::parse($data['from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Created until '.Carbon::parse($data['until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['event_start'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['event_end'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),

            ])
            ->actions([
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Szerkesztés')->link(),
                Tables\Actions\DeleteAction::make()->label(false)->tooltip('Törlés'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
