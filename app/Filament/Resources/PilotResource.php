<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PilotResource\Pages;
use App\Models\Pilot;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
/* saját use-ok */
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PilotResource extends Resource
{
    protected static ?string $model = Pilot::class;

    protected static ?string $navigationIcon = 'iconoir-user-square';

    protected static ?string $modelLabel = 'pilóta';

    protected static ?string $pluralModelLabel = 'pilóták';

    protected static ?string $navigationGroup = 'Alapadatok';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Section::make()
                            ->schema([
                                Forms\Components\Fieldset::make('Pilóta adatai')
                                    ->schema([
                                        Forms\Components\TextInput::make('lastname')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Adjon egy fantázianevet a légijárműnek. Érdemes olyan nevet választani, amivel könnyedén azonosítható lesz az adott légijármű.')*/
                                            ->label('Vezetéknév')
                                            ->placeholder('Gipsz')
                                            ->required(),
                                        Forms\Components\TextInput::make('firstname')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            ->label('Keresztnév')
                                            ->placeholder('Jakab')
                                            ->required(),
                                        Forms\Components\TextInput::make('phone')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            ->tel()
                                            ->label('Telefonszám')
                                            ->prefixIcon('tabler-device-mobile')
                                            ->placeholder('+36_________')
                                            ->mask('+9999999999999')
                                            ->maxLength(30)
                                            ->required(),
                                        Forms\Components\TextInput::make('pilot_license_number')
                                            /*->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Ide a légijármű lajstromjelét adja meg.')*/
                                            ->label('Pilóta engedély azonosító')
                                            ->prefixIcon('tabler-id-badge-2')
                                            ->placeholder('PPL-SEP'),
                                    ])->columns([
                                        'sm' => 1,
                                        'md' => 2,
                                        'lg' => 2,
                                        'xl' => 4,
                                        '2xl' => 4,
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('lastname')
                    ->label('Név')
                    ->searchable(['lastname', 'firstname'])
                    ->formatStateUsing(function ($state, Pilot $pilot) {
                        return $pilot->lastname.' '.$pilot->firstname;
                    }),
                Tables\Columns\TextColumn::make('pilot_license_number')
                    ->label('Pilóta engedély')
                    ->searchable()
                    ->visibleFrom('md'),
                Tables\Columns\TextColumn::make('phone')->label('Telefonszám')
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()->native(false),
            ])
            ->actions([
                /*
                Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Megtekintés')->link(),
                Tables\Actions\Action::make('delete')->icon('heroicon-m-trash')->color('danger')->hiddenLabel()->tooltip('Törlés')->link()->requiresConfirmation()->action(fn ($record) => $record->delete()),
                */
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
            'index' => Pages\ListPilots::route('/'),
            'create' => Pages\CreatePilot::route('/create'),
            /*'view' => Pages\ViewPilot::route('/{record}'),*/
            'edit' => Pages\EditPilot::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string //ez kiírja a menü mellé, hogy mennyi pilóta van már rögzítve
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string) $modelClass::all()->count();
    }
}
