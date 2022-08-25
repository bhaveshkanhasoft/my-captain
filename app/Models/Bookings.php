<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Bookings extends Model
{
    public const NUMBER_OF_GUESTS_LABEL = 'numOfGuests';
    public const DATE_LABEL = 'date';
    public const CACHE_KEY = 'booking';

    /**
     * Function to obtain the array of current bookings from cache
     *
     * @return array
     */
    public static function getCurrentBookings(): array
    {
        $bookingsJson = Redis::get( Bookings::CACHE_KEY );
        $currentBookings = json_decode( $bookingsJson, true );
        return $currentBookings;
    }

    /**
     * To get all the bookings from cache and prepare an array for response
     *
     * @return array
     */
    public static function readAllBookings(): array
    {
        $response = [];
        $bookings = Bookings::getCurrentBookings();
        if ( is_array ( $bookings ) && count( $bookings ) > 0 ) {
            foreach ( $bookings as $bookingDetails ) {
                $currentBookings = isset ( $response[ $bookingDetails[ Bookings::DATE_LABEL ] ] ) ? $response[ $bookingDetails[ Bookings::DATE_LABEL ] ] : 0;
                $response[ $bookingDetails[ Bookings::DATE_LABEL ] ] = $currentBookings + $bookingDetails[ Bookings::NUMBER_OF_GUESTS_LABEL ];
            }
        }
        return $response;
    }
}
