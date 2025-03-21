<?php

namespace App\Livewire;

use App\Filament\Resources\PendingcouponResource\Pages\ListPendingcoupons;
use App\Models\Coupon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RegionFilter extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public array $tableFilters = [];

    public function mount(): void
    {
        $this->form->fill(
            isset($this->tableFilters['region_id']['values'])
                ? ['region_id' => $this->tableFilters['region_id']['values']]
                : []
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('region_id')
                    ->label(false)
                    ->relationship('likedRegions', 'name')
                    ->placeholder('Érdekelt régió szűrő')
                    ->preload()
                    ->searchable(false)
                    ->multiple()
                    ->live(),
            ])
            ->statePath('data')
            ->model(Coupon::class);
    }

    public function updated()
    {
        $data = ['region_id' => [
            'values' => $this->data['region_id'],
        ]];

        $this->dispatch('regionFilterUpdated', $data)->to(ListPendingcoupons::class);
    }

    public function render(): View
    {
        return view('livewire.region-filter');
    }
}
