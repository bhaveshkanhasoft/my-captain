<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class BookingsController extends Controller
{

	public $bookableDays = [
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
	];

	public $bookableMonth = [
		'June',
		'July',
		'August',
	];

	/**
	 * Returns a listing of bookings
	 *
	 * @return array
	 */
	public static function index(): array
	{
		$bookingsJson = Redis::get( 'booking' );
		return json_decode( $bookingsJson, true );
	}

	/**
	 * Create a new booking if slots are available
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store( Request $request ): JsonResponse
	{
        $validate_field = array( 'date' => $request->date, 'numOfGuests' => $request->numOfGuests );

        $validator = Validator::make($validate_field, [
            'date' => 'required',
            'numOfGuests' => 'required|max:8',
        ]);

		if ( $validator->fails() ) {
			return response()->json( [ 'msg' => 'Provided parameters are not valid' ] );
		}

		$date = Carbon::createFromFormat('d/m/Y', $request->date)->format('d/m/Y');
		$numOfGuests = $request->numOfGuests;

		if ( ! $this->isValidDate( $date ) ) {
			return response()->json( [ 'msg' => 'Booking can not be made for selected date, please try another date.' ] );
		}

		$bookingsJson = Redis::get( 'booking' );
		$currentBookings = json_decode( $bookingsJson, true );

		$bookings = ( isset( $currentBookings[$date] ) && $currentBookings[$date] != null ) ? $currentBookings[$date] : 0;

		$newTotalGuests = $bookings + $numOfGuests;
		if ( ( $newTotalGuests ) <= 8 ) {
			$currentBookings[$date] = $newTotalGuests;
			$msg = "Booking has been made successfully.";
		} else {
			$msg = "Insufficient slots available.";
		}

		Redis::set( 'booking', json_encode( $currentBookings ) );

		return response()->json( [ 'msg' => $msg ] );
	}

	/**
	 * To validate the provided booking date is valid or not
	 *
	 * @param string $date
	 * @return boolean
	 */
	public function isValidDate(string $date): bool
	{
		$bookingDate = Carbon::createFromFormat('d/m/Y', $date);
		$bookingDay = $bookingDate->format('l');
		$bookingMonth = $bookingDate->format('F');
		$bookingYear = $bookingDate->format('Y');

		$now = Carbon::now();
		$currentYear = $now->format('Y');
		$nextYear = $currentYear + 1;

		if ( in_array( $bookingDay, $this->bookableDays ) &&
			in_array( $bookingMonth, $this->bookableMonth ) &&
			( $bookingYear == $currentYear || $bookingYear == $nextYear ) &&
			$bookingDate->gt($now) ) {
			return true;
		}
		return false;
	}
}
