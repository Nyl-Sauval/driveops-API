<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehiculeController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\InvoiceController;

// AUTH
// LOGIN
Route::post('login', [AuthController::class, 'login'])->name('login')->middleware('throttle:10,1');
// LOGOUT
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
// REGISTER
Route::post('register', [AuthController::class, 'register'])->name('register')->middleware('throttle:10,1');
// GOOGLE LOGIN
Route::post('google-login', [AuthController::class, 'googleLogin'])->name('google-login')->middleware('throttle:10,1');
// REFRESH TOKEN
Route::post('refresh-token', [AuthController::class, 'refresh'])->name('refresh-token')->middleware('auth:sanctum');
// ME
Route::get('me', [AuthController::class, 'me'])->name('me')->middleware(['auth:sanctum', 'check.token.expiration']);

// USER
//INDEX
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
// RELATIONS
Route::get('users/{user}/vehicles', [UserController::class, 'getUserVehicules'])->name('users.vehicules')->middleware('auth:sanctum');
Route::get('users/{user}/maintenances', [MaintenanceController::class, 'maintenancesByUser'])->name('users.maintenances')->middleware('auth:sanctum');
Route::get('users/{user}/invoices', [InvoiceController::class, 'invoicesByUser'])->name('users.invoices')->middleware('auth:sanctum');

// VEHICULE
// INDEX
Route::apiResource('vehicules', VehiculeController::class)->middleware('auth:sanctum');
// RELATIONS
Route::get('vehicules/{vehicule}/maintenances', [MaintenanceController::class, 'maintenancesByVehicule'])->name('vehicules.maintenances')->middleware('auth:sanctum');
Route::get('vehicules/{vehicule}/invoices', [InvoiceController::class, 'invoicesByVehicule'])->name('vehicules.invoices')->middleware('auth:sanctum');

// MAINTENANCE
// INDEX
Route::apiResource('maintenances', MaintenanceController::class)->middleware('auth:sanctum');
// SHOW
Route::get('maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show')->middleware('auth:sanctum');
// UPDATE
Route::put('maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update')->middleware('auth:sanctum');
// DELETE
Route::delete('maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy')->middleware('auth:sanctum');
// RELATIONS
Route::get('maintenances/{maintenance}/invoices', [InvoiceController::class, 'invoicesByMaintenance'])->name('maintenances.invoices')->middleware('auth:sanctum');

// INVOICE
// INDEX
Route::apiResource('invoices', InvoiceController::class)->middleware('auth:sanctum');
// SHOW
Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show')->middleware('auth:sanctum');
// UPDATE
Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update')->middleware('auth:sanctum');
// DELETE
Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy')->middleware('auth:sanctum');
