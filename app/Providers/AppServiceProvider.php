<?php

namespace App\Providers;

use App\Filament\Resources\CouponResource;
use App\Http\Responses\LogoutResponse;
use App\Models\Coupon;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
            function () {
                if (auth()->user() && ! auth()->user()->hasRole(['admin', 'super_admin'])) {
                    $coupons_not_filled_with_passengers = 0;
                    foreach (Coupon::all() as $coupon) {
                        if ($coupon->missingData) {
                            $coupons_not_filled_with_passengers++;
                        }
                    }
                    if ($coupons_not_filled_with_passengers > 0 && !str_contains(env('APP_URL').$_SERVER['REQUEST_URI'], CouponResource::getUrl())) {
                        Notification::make()
                            ->title('Hiányzó utasadatok!')
                            ->body('Repülésre történő jelentkezéshez töltse fel elérhető kuponja utasainak adatait.')
                            ->iconColor('danger')
                            ->color('danger')
                            ->icon('tabler-alert-triangle')
                            ->persistent()
                            ->actions(function () {
                                return [
                                    Action::make('redirect')
                                        ->button()
                                        ->label('Ugrás a kitöltendő kuponokhoz')
                                        ->url(CouponResource::getUrl()),
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
                    ->url('/')
                    ->icon('iconoir-hot-air-balloon')
                    ->activeIcon('iconoir-hot-air-balloon')
                    ->sort(1),
            ]);
        });
    }
}
