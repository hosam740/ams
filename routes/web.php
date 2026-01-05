<?php

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Authentication\AuthController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard/dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('show.login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('show.register');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::middleware('auth')->group(function() {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::resource('assets', AssetController::class)->names('assets');
    Route::resource('properties', PropertyController::class)->names('properties');
    Route::resource('contracts', ContractController::class)->names('contracts');
    Route::resource('tenants', TenantController::class)->names('tenants');
    
        /*
    | Units belong to Properties
    | - Units must be created within a specific Property context
    | - Avoids using query strings like ?property_id
    | - Keeps URLs RESTful and meaningful
    |
    | Example:
    |   /properties/3/units/create
    */
    Route::resource('properties.units', UnitController::class)
        ->shallow() // After creation, access units directly (e.g. /units/{unit}/edit)
        ->names('units');

    Route::resource('payments', PaymentController::class)->names('payments');
});