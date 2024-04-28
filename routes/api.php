<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RosterFileController;
use App\Http\Controllers\RoasterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('roaster')->group(function () {
    Route::post('/upload-file', [RosterFileController::class, 'uploadFile']);
    Route::get('/all-files', [RosterFileController::class, 'getAllFiles']);
    Route::get('/file/{id}', [RosterFileController::class, 'getFileById']);
    Route::get('/events', [RoasterController::class, 'getEventsBetweenDates']);
    Route::get('/events/next-week', [RoasterController::class, 'getActivitiesForNextWeek']);
    Route::get('/flights', [RoasterController::class, 'getFlightsByLocation']);
});