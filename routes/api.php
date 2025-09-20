<?php

use App\Http\Controllers\slidersController;
use App\Http\Controllers\site_content_controller;
use App\Http\Controllers\admin\authController;
use App\Http\Controllers\admin\RolesPermissionController;
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

Route::prefix('roles')->middleware('auth:sanctum','check.superadmin:super admin')->group(function(){
    Route::get('get',[RolesPermissionController::class,'index']);
    Route::post('add',[RolesPermissionController::class,'store']);
    Route::post('update/{roleId}',[RolesPermissionController::class,'update']);
    Route::delete('delete/{roleId}',[RolesPermissionController::class,'destroy']);
});

Route::prefix('permissions')->middleware('auth:sanctum','check.superadmin:super admin')->group(function(){
    Route::get('get',[RolesPermissionController::class,'permissionsIndex']);
    Route::post('add',[RolesPermissionController::class,'permissionStore']);
    Route::post('update/{id}',[RolesPermissionController::class,'permissionUpdate']);
    Route::delete('delete/{id}',[RolesPermissionController::class,'permissionDestroy']);

});



Route::get('getSliders',[slidersController::class , 'index']);
Route::post('createSlider',[slidersController::class,'create'])->middleware('auth:sanctum');


Route::prefix('site-content')->group(function (){
    Route::get('get',[site_content_controller::class,'index']);
});