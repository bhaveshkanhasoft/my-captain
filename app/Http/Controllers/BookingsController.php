<?php

namespace App\Http\Controllers;

use App\Models\Bookings;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class BookingsController extends Controller
{
	/**
	 * Returns a listing of bookings
	 *
	 * @return array
	 */
	public static function read(): array
	{
        	return Bookings::readAllBookings();
	}

	/**
	 * Create a new booking if slots are available
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function create( Request $request ): JsonResponse
	{
		$validate_field = array( Bookings::DATE_LABEL => $request->date, Bookings::NUMBER_OF_GUESTS_LABEL => $request->numOfGuests );
		$validator = Validator::make( $validate_field, [
			Bookings::DATE_LABEL => 'required|bookable_date',
			Bookings::NUMBER_OF_GUESTS_LABEL => 'required|max:8',
        	],
        	[
			'date.bookable_date'=> 'You can not proceed with the selected booking date, please select another date.',
		]);

		if ( $validator->fails() ) {
			return response()->json( [ 'msg' => $validator->errors()->first() ] );
		}

		$currentBookings = Bookings::getCurrentBookings();
		$values = $request->all();
		$values[ 'created_at' ] = Carbon::now()->format( 'Y-m-d' );
		$currentBookings[] = $values;
		$msg = "Booking has been made successfully.";
		Redis::set( Bookings::CACHE_KEY, json_encode( $currentBookings ) );
		
		return response()->json( [ 'msg' => $msg ] );
	}
}
