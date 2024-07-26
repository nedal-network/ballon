<?php

namespace App\Providers;

use App\Models\Coupon;
use App\Enums\CouponStatus;
use Filament\Facades\Filament;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Notifications\Notification;
use App\Filament\Resources\CouponResource;
use Filament\Notifications\Actions\Action;
use Filament\Support\Facades\FilamentView;
use App\Http\Responses\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Model::unguard();

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            function()
            {
                if (auth()->user() && !auth()->user()->hasRole(['admin', 'super_admin'])) {
                    $coupons_not_filled_with_passengers = 0;
                    foreach (Coupon::all() as $coupon) 
                    {
                        if ($coupon->missingData) 
                        {
                            $coupons_not_filled_with_passengers++;
                        }
                    }
                    if($coupons_not_filled_with_passengers>0)
                    {
                        Notification::make()
                        ->title('Hiányzó utasadatok!')
                        ->body('Repülésre történő jelentkezéshez töltse fel elérhető kuponja utasainak adatait.')
                        ->iconColor('danger')
                        ->color('danger')
                        ->icon('tabler-alert-triangle')
                        ->persistent()
                        ->actions(function () {
                            if (str_contains(env('APP_URL') . $_SERVER['REQUEST_URI'], CouponResource::getUrl())) {
                                return [];
                            }
                            
                            return [
                                Action::make('redirect')
                                    ->button()
                                    ->label('Ugrás a kitöltendő kuponokhoz')
                                    ->url(CouponResource::getUrl() . '?activeTab=Figyelmeztet%C3%A9sek')
                            ];
                        })
                        ->send();
                    }
                }
            }
        );
        
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn (): string => Blade::render('@vite([\'resources/css/checking.css\', \'resources/css/list-checkins.css\'])'),
        );

        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make('go_home')
                    ->label('Vissza a kezdőlapra')
                    //->url('/', shouldOpenInNewTab: true)
                    ->url('/')
                    ->icon('iconoir-hot-air-balloon')
                    ->activeIcon('iconoir-hot-air-balloon')
                    ->sort(1),
            ]);
        });
    }
}
