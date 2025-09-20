<?php

use App\Http\Controllers\slidersController;
use App\Http\Controllers\site_content_controller;
use App\Http\Controllers\authController;
use App\Http\Middleware\CheckSuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function (){

    Route::post('login',[authController::class,'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout',[authController::class,'logout']);
        Route::post('addUser',[authController::class,'addUser']);
    });
});


Route::prefix('user')->middleware('auth:sanctum','check.superadmin:super admin')->group(function(){
    Route::post('add',[authController::class,'addUser']);
    Route::get('get/{userId}',[authController::class,'getUser']);
    Route::post('delete/{userId}',[authController::class,'deleteUser']);
    Route::post('update/{userId}',[authController::class,'updateUser']);
    Route::get('get',[authController::class,'index']);
});


Route::get('getSliders',[slidersController::class , 'index'])->middleware('auth:sanctum');
Route::post('createSlider',[slidersController::class,'create']);


Route::prefix('site-content')->group(function (){
    Route::get('get',[site_content_controller::class,'index']);
});