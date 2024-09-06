<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            //ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Név'),
            ExportColumn::make('email')->label('E-mail cím'),
            ExportColumn::make('phone')->label('Telefonszám'),
            //ExportColumn::make('email_verified_at'),
            ExportColumn::make('created_at')->label('Ekkor regisztrált'),
            ExportColumn::make('last_login_at')->label('Utoljára ekkor volt itt'),
            //ExportColumn::make('updated_at'),
            //ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A felhasználók exportálása kész. és '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exportálva.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' exportálás sikertelen.';
        }

        return $body;
    }
}
