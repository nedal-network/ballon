<?php

namespace App\Filament\Resources\PendingcouponResource\Pages;

use App\Enums\CouponStatus;
use App\Filament\Forms\Components\CustomDatePicker;
use App\Filament\Resources\PendingcouponResource;
use App\Models\Coupon;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;

class EditPendingcoupon extends EditRecord
{
    protected static string $resource = PendingcouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (Carbon::parse($data['expiration_at']) < today() && $this->record->status != CouponStatus::Applicant) {
            $data['status'] = CouponStatus::Expired;
        }

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Action::make('Kiegészítő jegy')
                ->form(PendingcouponResource::getVirtualCouponSchema())
                ->action(function (array $data, Coupon $record): void {
                    $virtual_coupon = new Coupon();
                    $virtual_coupon->parent_id = $record->id;
                    $virtual_coupon->user_id = $record->user_id;
                    $virtual_coupon->coupon_code = 'virtual'.random_int(10000, 99999);
                    $virtual_coupon->source = 'Kiegészítő';
                    $virtual_coupon->adult = $data['adult'];
                    $virtual_coupon->children = $data['children'];
                    $virtual_coupon->tickettype_id = $record->tickettype_id;
                    $virtual_coupon->status = CouponStatus::CanBeUsed;
                    $virtual_coupon->expiration_at = $record->expiration_at;
                    $virtual_coupon->total_price = $data['total_price'];
                    $virtual_coupon->description = $data['description'];
                    $virtual_coupon->created_at = Carbon::now()->toDateTimeString();
                    $virtual_coupon->save();
                }),

            Action::make('Ajándék jegy')
                ->form([
                    Fieldset::make('Ajándék jegy részletei')
                        ->schema([
                            TextInput::make('adult')
                                ->label('Felnőtt')
                                ->prefixIcon('tabler-friends')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->minValue(1)
                                ->minLength(1)
                                ->maxLength(10)
                                ->suffix(' fő'),
                            TextInput::make('children')
                                ->label('Gyermek')
                                ->prefixIcon('tabler-horse-toy')
                                ->required()
                                ->numeric()
                                ->default(0)
                                ->minLength(1)
                                ->maxLength(10)
                                ->suffix(' fő'),
                            Select::make('tickettype_id')
                                ->label('Jegytípus')
                                ->prefixIcon('heroicon-o-ticket')
                                ->required()
                                ->native(false)
                                ->relationship(
                                    name: 'tickettype',
                                    modifyQueryUsing: fn ($query) => $query->orderBy('aircrafttype')->orderBy('default', 'desc')->orderBy('name'),
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->aircrafttype->getLabel()} - {$record->name}"),
                            CustomDatePicker::make('expiration_at')
                                ->label('Felhasználható')
                                ->prefixIcon('tabler-calendar')
                                ->weekStartsOnMonday()
                                ->format('Y-m-d')
                                ->displayFormat('Y.m.d.')
                                ->default(now()),
                        ])->columns(2),

                ])
                ->action(function (array $data, Coupon $record): void {
                    $virtual_coupon = new Coupon();
                    $virtual_coupon->user_id = $record->user_id;
                    $virtual_coupon->coupon_code = 'gift'.random_int(10000, 99999);
                    $virtual_coupon->source = 'Ajándék';
                    $virtual_coupon->adult = $data['adult'];
                    $virtual_coupon->children = $data['children'];
                    $virtual_coupon->tickettype_id = $data['tickettype_id'];
                    $virtual_coupon->status = CouponStatus::CanBeUsed;
                    $virtual_coupon->expiration_at = $data['expiration_at'];
                    $virtual_coupon->created_at = Carbon::now()->toDateTimeString();
                    $virtual_coupon->save();
                }),
            $this->getCancelFormAction(),
        ];
    }

    public function saveAnother(): void
    {
        $resources = static::getResource();
        $this->save(false);
        $this->redirect($resources::getUrl('create'));
    }
}
