<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\Activity\ActivityController;
use App\Http\Controllers\DailyActivityController;


Route::controller(ActivityController::class)->prefix('activities')->group(function () {
    Route::get('', 'index')->name('admin.activities.index');

    Route::get('get', 'get')->name('admin.activities.get');

    Route::post('create', 'store')->name('admin.activities.store');

    Route::get('edit/{id}', 'edit')->name('admin.activities.edit');

    Route::put('edit/{id}', 'update')->name('admin.activities.update');

    Route::get('download/{id}', 'download')->name('admin.activities.file_download');

    Route::delete('{id}', 'destroy')->name('admin.activities.delete');

    Route::post('mass-update', 'massUpdate')->name('admin.activities.mass_update');

    Route::post('mass-destroy', 'massDestroy')->name('admin.activities.mass_delete');
});

// Daily Activities
Route::prefix('admin/daily-activities')->controller(DailyActivityController::class)->group(function () {
    Route::get('/', 'index')->name('admin.daily_activities.index');

    // create (Option A)
    Route::get('/form', 'form')->name('admin.daily_activities.form');
    Route::post('/store', 'store')->name('admin.daily_activities.store');

    // NEW: view one submission
    Route::get('/{dailyActivity}', 'show')->name('admin.daily_activities.show');

    // NEW: edit/update (same-day only)
    Route::get('/{dailyActivity}/edit', 'edit')->name('admin.daily_activities.edit');
    Route::put('/{dailyActivity}', 'update')->name('admin.daily_activities.update');

    // NEW: delete (same-day only)
    Route::delete('/{dailyActivity}', 'destroy')->name('admin.daily_activities.destroy');
});


