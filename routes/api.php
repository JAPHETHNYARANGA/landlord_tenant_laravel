<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandlordController;
use App\Http\Controllers\MaintenanceTicketController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\TenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);

Route::post('reset-password', [AuthController::class, 'resetPassword']);


Route::resource('admins', AdminController::class);
Route::resource('landlords', LandlordController::class);
Route::resource('tenants', TenantController::class);

Route::apiResource('properties', PropertiesController::class);

Route::apiResource('maintenance-tickets', MaintenanceTicketController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);


   

});