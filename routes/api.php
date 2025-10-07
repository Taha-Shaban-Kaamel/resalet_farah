<?php

use App\Http\Controllers\slidersController;
use App\Http\Controllers\sitContentController;
use App\Http\Controllers\admin\authController;
use App\Http\Controllers\admin\userController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\admin\RolesPermissionController;
use App\Http\Controllers\BoardOfDirctorsController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function (){
    Route::post('login',[authController::class,'login']);
    Route::post('forgetPassword',[authController::class,'forgetPassword']);
    Route::post('verfiyCode',[authController::class,'verfiyCode']);
    Route::post('resetPassword',[authController::class,'resetPassword']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout',[authController::class,'logout']);
        Route::post('addUser',[authController::class,'addUser']);
        Route::get('me',[authController::class,'me']);
    });
});


Route::prefix('user')->middleware('auth:sanctum','check.superadmin:super admin')->group(function(){
    Route::post('add',[userController::class,'addUser']);
    Route::get('get/{userId}',[userController::class,'getUser']);
    Route::post('delete/{userId}',[userController::class,'deleteUser']);
    Route::post('update/{userId}',[userController::class,'updateUser']);
    Route::get('get',[userController::class,'index']);
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
    Route::get('get',[sitContentController::class,'index']);
    Route::post('create',[sitContentController::class,'store']);
    Route::post('update/{id}',[sitContentController::class,'update']);
    Route::post('delete/{id}',[sitContentController::class,'destroy']);
});

Route::prefix('board-of-dirctors')->group(function(){
    Route::get('get',[BoardOfDirctorsController::class,'index']);
    Route::get('get/{id}',[BoardOfDirctorsController::class,'show']);
    Route::middleware('auth:sanctum','check.superadmin:super admin')->group(function(){
        Route::post('create',[BoardOfDirctorsController::class,'store']);
        Route::post('update/{id}',[BoardOfDirctorsController::class,'update']);
        Route::post('delete/{id}',[BoardOfDirctorsController::class,'destroy']);
    });
});

Route::prefix('services')->group(function(){
    Route::get('get',[ServiceController::class,'index']);
    Route::get('get/{id}',[ServiceController::class,'show']);
    Route::middleware('auth:sanctum','check.superadmin:super admin')->group(function(){
        Route::post('create',[ServiceController::class,'store']);
        Route::post('update/{id}',[ServiceController::class,'update']);
        Route::post('delete/{id}',[ServiceController::class,'destroy']);
    });
});

Route::prefix('news')->group(function(){
    Route::get('get',[NewsController::class,'index']);
    Route::get('get/{id}',[NewsController::class,'show']);
    Route::middleware('auth:sanctum','check.superadmin:super admin')->group(function(){
        Route::post('create',[NewsController::class,'store']);
        Route::post('update/{id}',[NewsController::class,'update']);
        Route::post('delete/{id}',[NewsController::class,'destroy']);
    });
});