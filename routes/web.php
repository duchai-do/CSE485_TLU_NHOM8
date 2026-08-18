<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BedController;

Route::resource('buildings', BuildingController::class)->only(['index', 'store', 'destroy']);
Route::resource('rooms', RoomController::class)->only(['index', 'store', 'destroy']);
Route::resource('beds', BedController::class)->only(['index', 'store', 'destroy']);

Route::get('/', function () {
    return redirect()->route('buildings.index');
});

require __DIR__.'/member3.php';