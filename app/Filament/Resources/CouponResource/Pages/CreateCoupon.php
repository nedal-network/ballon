<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Enums\CouponStatus;
use App\Filament\Resources\CouponResource;
use App\Models\Coupon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function getFormActions(): array
    {
        return [
            parent::getCreateFormAction()->label(fn () => in_array($this->data['source'], ['Ballonozz', 'Meglepkék']) ? 'Ellenőrzés' : 'Létrehozás'),
            parent::getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $checking_the_existence_of_a_coupon = Coupon::where('coupon_code', $data['coupon_code'])->get()->count();
        if ($checking_the_existence_of_a_coupon != 0) {
            Notification::make()
                ->title('Hibás kuponkódott adott meg!')
                ->body('Ilyen kóddal már létezik kupon a rendszerben!')
                ->color('danger')
                ->icon('tabler-alert-triangle')
                ->danger()
                ->send();
            $this->halt();
        }
        if ($checking_the_existence_of_a_coupon == 0) {
            if ($data['source'] == 'Ballonozz') {
                try {
                    $response_coupon = Http::withBasicAuth(env('BALLONOZZ_API_USER_KEY'), env('BALLONOZZ_API_SECRET_KEY'))->get('https://ballonozz.hu/wp-json/wc/v3/orders/'.$data['coupon_code']);

                    //Felőtt(3db->3f): 1567
                    //Családi(1db->2f+2gy): 1508 érvénes
                    //1526 nem érvényes
                    if ($response_coupon->successful()) {
                        $coupons_data = $response_coupon->json();
                        $payment_total_price = ($coupons_data['total']);
                        //vásárlás dátumának lekérése
                        $payment_datetime_completed = ($coupons_data['date_completed']);
                        $payment_date_completed = substr($payment_datetime_completed, 0, 10);
                        //kupon lejáratának számítása
                        $payment_date_completed_plus_one_year = strtotime(date('Y-m-d', strtotime($payment_date_completed)).'+1 year');
                        $coupon_expiration_date = date('Y-m-d', $payment_date_completed_plus_one_year);
                        //kupon felhasználghatóságának türelmi dátumának számítása
                        $payment_date_completed_plus_one_year_plus_one_month = strtotime(date('Y-m-d', strtotime($coupon_expiration_date)).'+1 month');
                        $coupon_expiration_grace_date = date('Y-m-d', $payment_date_completed_plus_one_year_plus_one_month);

                        if ($coupons_data['status'] == 'completed') {
                            foreach ($coupons_data['line_items'] as $coupon) {
                                $response_item_nums = $coupon['quantity'];
                                $response_product_id = $coupon['product_id'];

                                try {
                                    $response_product_attributes = Http::withBasicAuth(env('BALLONOZZ_API_USER_KEY'), env('BALLONOZZ_API_SECRET_KEY'))->get('https://ballonozz.hu/wp-json/wc/v3/products/'.$response_product_id);
                                    if ($response_product_attributes->successful()) {
                                        $product_attributes = $response_product_attributes->json();
                                        $data['tickettype_id'] = ($product_attributes['attributes'][0]['options'][0]) * 1;
                                        $data['adult'] = ($product_attributes['attributes'][1]['options'][0]) * $response_item_nums;
                                        $data['children'] = ($product_attributes['attributes'][2]['options'][0]) * $response_item_nums;
                                        $data['status'] = CouponStatus::CanBeUsed;
                                        $data['expiration_at'] = $coupon_expiration_date;
                                        $data['total_price'] = $payment_total_price;
                                    } else {
                                        Notification::make()
                                            ->title('Váratlan hiba történt!')
                                            ->body('Kérjük próbálkozzon újra később.')
                                            ->color('danger')
                                            ->icon('tabler-alert-triangle')
                                            ->danger()
                                            ->send();

                                        $this->halt();
                                    }
                                } catch (\Throwable $th) {
                                    if (! $th instanceof \Filament\Support\Exceptions\Halt) {
                                        Notification::make()
                                            ->title('Váratlan hiba történt!')
                                            ->body('Kérjük próbálkozzon újra később.')
                                            ->color('danger')
                                            ->icon('tabler-alert-triangle')
                                            ->danger()
                                            ->send();
                                    }

                                    $this->halt();
                                }
                            }
                        } else {
                            Notification::make()
                                ->title('Probléma adódott a megadott kuponkóddal!')
                                ->body('Bővebb információ érdekében, kérjük vegye fel a kapcsolatot az Ön kuponjának értékesítőjével!')
                                ->color('danger')
                                ->icon('tabler-alert-triangle')
                                ->danger()
                                ->send();

                            $this->halt();
                        }
                    } else {
                        Notification::make()
                            ->title('Hibás kuponkódott adott meg!')
                            ->color('danger')
                            ->icon('tabler-alert-triangle')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                } catch (\Throwable $th) {
                    if (! $th instanceof \Filament\Support\Exceptions\Halt) {
                        Notification::make()
                            ->title('Váratlan hiba történt!')
                            ->body('Kérjük próbálkozzon újra később.')
                            ->color('danger')
                            ->icon('tabler-alert-triangle')
                            ->danger()
                            ->send();
                    }

                    $this->halt();
                }
            } elseif ($data['source'] == 'Meglepkék') {
                try {
                    $response_coupon = Http::get('https://meglepkek.hu/api/v2/voucher/validate?token=12bd1f2b664790b6e9bddb2db6663263aa796316&partner_id=254&number='.$data['coupon_code']);
                    if ($response_coupon->successful()) {
                        $coupons_data = $response_coupon->json();
                        if ($coupons_data['status']) {
                            $payment_total_price = $coupons_data['voucher_price'];
                            $coupon_expiration_date = substr($coupons_data['voucher_end_date'], 0, 10);

                            $members = array_map(fn ($item) => intval(trim($item)), explode('+', str_replace(['részére', 'felnőtt', 'gyerek'], '', $coupons_data['product_options'][0]['selected'])));

                            $data['tickettype_id'] = 2; //Normál repülés
                            $data['adult'] = $members[0];
                            $data['children'] = $members[1] ?? 0;
                            $data['status'] = CouponStatus::CanBeUsed;
                            $data['expiration_at'] = $coupon_expiration_date;
                            $data['total_price'] = $payment_total_price;
                        } else {
                            Notification::make()
                                ->title('Hibás kuponkódott adott meg!')
                                ->color('danger')
                                ->icon('tabler-alert-triangle')
                                ->danger()
                                ->send();

                            $this->halt();
                        }

                    } else {
                        Notification::make()
                            ->title('Hibás kuponkódott adott meg!')
                            ->color('danger')
                            ->icon('tabler-alert-triangle')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                } catch (\Throwable $th) {
                    if (! $th instanceof \Filament\Support\Exceptions\Halt) {
                        Notification::make()
                            ->title('Váratlan hiba történt!')
                            ->body('Kérjük próbálkozzon újra később.')
                            ->color('danger')
                            ->icon('tabler-alert-triangle')
                            ->danger()
                            ->send();
                    }

                    $this->halt();
                }

            }
        }

        if ($checking_the_existence_of_a_coupon == 0 && ! in_array($this->data['source'], ['Ballonozz', 'Meglepkék'])) {
            $data['tickettype_id'] = null;
            $data['status'] = CouponStatus::UnderProcess;
        }

        return $data;
    }

    /*
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    */
    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->visible(false);
    }
}
