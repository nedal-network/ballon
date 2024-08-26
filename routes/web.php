<?php

use App\Livewire\Home;
use App\Models\Aircraft;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CouponsController;
use App\Http\Controllers\ExportController;
use App\Models\Tickettype;
use Filament\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', Home::class)->name('home');

Route::get('/test', function(){
    //dump(env('AIRCRAFT_PASSENGER_LIMIT', 30));
    //dd(Aircraft::flyable(3, false, false));

        $response_coupon = Http::withBasicAuth(env('BALLONOZZ_API_USER_KEY'), env('BALLONOZZ_API_SECRET_KEY'))->get('https://ballonozz.hu/wp-json/wc/v3/orders/'.'1567');
        //Felőtt: 1567
        //Családi: 1508
        if ($response_coupon->successful())
        {
            //dd($response_coupon->json());
            $coupons_data = $response_coupon->json();

            $payment_datetime_completed = ($coupons_data['date_completed']);
            dump($payment_date_completed = substr($payment_datetime_completed, 0, 10));

            $payment_date_completed_plus_one_year = strtotime(date("Y-m-d", strtotime($payment_date_completed)) . "+1 year");
            dump($coupon_expiration_date = date('Y-m-d', $payment_date_completed_plus_one_year));

            $payment_date_completed_plus_one_year_plus_one_month = strtotime(date("Y-m-d", strtotime($coupon_expiration_date)) . "+1 month");
            dump($coupon_expiration_grace_date = date('Y-m-d', $payment_date_completed_plus_one_year_plus_one_month));

            //dump($date = strotime($payment_date_completed));
            foreach($coupons_data['line_items'] as $coupon)
            {
                //dump($item['product_id'], $item['quantity']);
                
                $response_item_nums = $coupon['quantity']; 
                $response_product_id = $coupon['product_id'];
                
                $response_product_attributes = Http::withBasicAuth(env('BALLONOZZ_API_USER_KEY'), env('BALLONOZZ_API_SECRET_KEY'))->get('https://ballonozz.hu/wp-json/wc/v3/products/'.$response_product_id);
                if ($response_product_attributes->successful())
                {
                    //dd($response_product_attributes->json());
                    $product_attributes = $response_product_attributes->json();
                    dump(($product_attributes['attributes'][0]['options'][0])*1);
                    dump(($product_attributes['attributes'][1]['options'][0])*$response_item_nums);
                    dump(($product_attributes['attributes'][2]['options'][0])*$response_item_nums);

                    /*foreach($product_attributes['attributes'] as $attribute)
                    {
                        //dump($attribute['options'][0]);
                    }*/
                }
            }
        }
});