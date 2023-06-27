<?php

use App\Http\Controllers\PlaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(PlaceController::class)->group(function () {
    Route::get('/places', 'index');
    Route::get('/places/{id}', 'show');
    Route::post('/places', 'create');
    Route::put('/places/{id}', 'update');
    Route::delete('/places/{id}', 'destroy');
});