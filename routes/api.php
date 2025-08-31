<?php

use App\Http\Controllers\slidersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('getSliders',[slidersController::class , 'index']);
Route::post('createSlider',[slidersController::class,'create']);