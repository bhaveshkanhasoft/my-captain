<?php

use App\Http\Controllers\BookingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('bookings')->group(function () {
	Route::post('/create', [BookingsController::class, 'create']);
	Route::get('/read', [BookingsController::class, 'read'])->name('booking-read');
});
