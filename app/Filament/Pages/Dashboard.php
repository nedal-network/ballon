<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Üdvözlöl a fedélzeten a Ballonozz.hu csapata!';

    protected static ?string $navigationIcon = 'tabler-home-heart';
}
