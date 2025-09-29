<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyActivityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Default Laravel welcome page (optional)
Route::get('/', function () {
    return view('welcome');
});

// Daily Activities routes (protected by admin login)
Route::prefix('admin')->middleware(['web', 'auth'])->group(function () {
    Route::get('/daily-activities', [DailyActivityController::class, 'index'])
        ->name('daily-activities.index');

    Route::get('/daily-activities/form', [DailyActivityController::class, 'form'])
        ->name('daily-activities.form');

    Route::get('/daily-activities/{dailyActivity}', [DailyActivityController::class, 'show'])
        ->name('daily-activities.show');

    Route::post('/daily-activities', [DailyActivityController::class, 'store'])
        ->name('daily-activities.store');

    Route::put('/daily-activities/{dailyActivity}', [DailyActivityController::class, 'update'])
        ->name('daily-activities.update');
});

