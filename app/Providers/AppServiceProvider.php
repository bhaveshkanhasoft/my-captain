<?php

namespace App\Providers;

use App\Models\Bookings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend( 'bookable_date', function ($attribute, $value, $parameters, $validator ) {
            $inputs = $validator->getData();
            $date = Carbon::createFromFormat( 'd/m/Y', $inputs[ Bookings::DATE_LABEL ] )->format( 'd/m/Y' );
            $numOfGuests = $inputs[ Bookings::NUMBER_OF_GUESTS_LABEL ];

            $bookingDate = Carbon::createFromFormat( 'd/m/Y', $date );
            $now = Carbon::now();

            if ( $bookingDate->isWeekday() &&
                ( $bookingDate->is( 'June' ) || $bookingDate->is( 'July' ) || $bookingDate->is( 'August' ) ) &&
                $now->diffInYears( $bookingDate ) < 2 &&
                $bookingDate->gt( $now ) ) {
                    $bookings = 0;
                    $currentBookings = Bookings::getCurrentBookings();
                    foreach ( $currentBookings as $bookingDetail ) {
                        $bookedDate = Carbon::createFromFormat( 'd/m/Y', $bookingDetail[ Bookings::DATE_LABEL ] )->format( 'd/m/Y' );
                        if ( $bookedDate === $date ) {
                            $bookings = $bookings + $bookingDetail[ Bookings::NUMBER_OF_GUESTS_LABEL ];
                        }
                    }
                    $newTotalGuests = $bookings + $numOfGuests;
                    return $newTotalGuests <= 8;
            }
            return false;
        });
    }
}
