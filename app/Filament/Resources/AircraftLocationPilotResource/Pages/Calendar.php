<?php

namespace App\Filament\Resources\AircraftLocationPilotResource\Pages;

use Carbon\Carbon;
use App\Models\Event;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use App\Models\AircraftLocationPilot;
use App\Enums\AircraftLocationPilotStatus;
use App\Filament\Resources\AircraftLocationPilotResource;

class Calendar extends Page
{
    protected static string $resource = AircraftLocationPilotResource::class;
    protected static ?string $title = 'Naptár Nézet';
    protected static string $view = 'filament.resources.aircraft-location-pilot-resource.pages.calendar';

    public $events;

    public function mount()
    {
        $extraevents = Event::all();
        foreach ($extraevents as $extraevent) {
            $extraeventcolor = 'rgb(255, 30, 220)';
            if ($extraevent->status == 1)
            {
                $this->events[] = [
                    'title' => $extraevent->name,
                    'start' => Carbon::parse($extraevent->start_date . ' 00:00:00')->format('Y-m-d H:i:s'),
                    'end'   => Carbon::parse($extraevent->end_date . ' 23:59:59')->format('Y-m-d H:i:s'),
                    'description' => '<div>Esemény: ' . ($extraevent->name ?? 'Ismeretlen') . '</div><div>'.($extraevent->description ?? 'Nincs leírás').'</div>',
                    'color' => $extraeventcolor
                ];
            }
        }

        $events = AircraftLocationPilot::all();
        foreach ($events as $event) {
            switch ($event->status) {
                case AircraftLocationPilotStatus::Draft:
                    $color = 'rgb(217, 119, 6)';
                    break;

                case AircraftLocationPilotStatus::Finalized:
                    $color = 'rgb(22, 163, 74)';
                    break;
                    
                    case AircraftLocationPilotStatus::Executed:
                    $color = '#71717a';
                    break;

                case AircraftLocationPilotStatus::Deleted:
                    $color = 'rgb(220, 38, 38)';
                    break;
                
                default:
                    $color = 'rgb(37, 99, 235)';
                    break;
            }
            
            $signed     = array_sum($event->coupons->map(fn ($coupon) => $coupon->membersCount)->toArray());
            $classified = array_sum($event->coupons->map(function ($coupon) {
                if ($coupon->pivot->status == 1) {
                    return $coupon->membersCount;
                }
                return 0;
            })->toArray());   

            $classifiedWeight = array_sum($event->coupons->map(function ($coupon) {
                if ($coupon->pivot->status == 1) {
                    return $coupon->passengers->sum('body_weight');
                }
                return 0;
            })->toArray());

            $exploded_time = explode(':', $event->period_of_time);
            $this->events[] = [
                'title' => $event->region->name . ' '  . $classified . '/' . $signed,
                'start' => Carbon::parse($event->date . ' ' . $event->time)->format('Y-m-d H:i:s'),
                'end'   => Carbon::parse($event->date . ' ' . $event->time)->addHours($exploded_time[0])->addMinutes($exploded_time[1])->format('Y-m-d H:i:s'),
                'description' => '<div>Helyszín: ' . ($event->location?->name ?? 'Ismeretlen') . '</div><div>Össz. tömeg: '. $classifiedWeight .'/' . $event->aircraft->payload_capacity . ' kg</div>',
                'color' => $color
            ];
        }
        $this->events = json_encode($this->events, true);
    }
}
