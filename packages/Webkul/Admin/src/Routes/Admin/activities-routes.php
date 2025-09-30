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
Route::prefix('daily-activities')->group(function () {
    Route::get('/', [DailyActivityController::class, 'index'])->name('admin.daily_activities.index');
    Route::get('form', [DailyActivityController::class, 'form'])->name('admin.daily_activities.form');
    Route::post('/store', [DailyActivityController::class, 'store'])->name('admin.daily_activities.store');
});

