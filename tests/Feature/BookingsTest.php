<?php

namespace Tests\Feature;

use DateTime;
use Tests\TestCase;

class BookingsTest extends TestCase
{
	/**
	 * A test to check booking list response
	 *
	 * @return void
	 */
	public function testCheckBookingListPage(): void
	{
		$response = $this->getJson('/api/bookings/read');
		$response->assertStatus(200);
	}


	/**
	 * A test to check today's booking should not happen.
	 *
	 * @return void
	 */
	public function testCreateBookingPastOrTodayDate(): void
	{
		$datetime = new DateTime();
		$date = $datetime->format('d/m/Y');
		$response = $this->post('/api/bookings/create', ['date' => $date, 'numOfGuests' => 5]);
		$this->assertEquals("Booking can not be made for selected date, please try another date.", $response['msg']);
		$response->assertStatus(200);
	}

	/**
	 * A test to check more than 8 person's booking should not happen.
	 *
	 * @return void
	 */
	public function testCreateBookingFutureDateMoreSlots()
	{
		$datetime = new DateTime('tomorrow');
		$date = $datetime->format('d/m/Y');
		$response = $this->post('/api/bookings/create', ['date' => $date, 'numOfGuests' => 11]);
		$this->assertEquals("Insufficient slots available.", $response['msg']);
		$response->assertStatus(200);
	}
}
