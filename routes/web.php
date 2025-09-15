<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartnerUserController; 
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

// Rutas de autenticación
Auth::routes();

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas accesibles para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/customers', [HomeController::class, 'customers'])->name('customers');
    Route::post('/customers', [HomeController::class, 'customers'])->name('customer');
    Route::get('/conversations', [HomeController::class, 'conversations'])->name('conversations');
    Route::post('/requests', [HomeController::class, 'requests'])->name('requests');
    Route::post('/notes', [HomeController::class, 'notes'])->name('notes');
    Route::get('/form', [HomeController::class, 'form'])->name('form');
    Route::get('/searchClient', [HomeController::class, 'searchClient'])->name('searchClient');
    Route::resource('users', UserController::class);
    
    Route::get('/partners/users/create', [PartnerUserController::class, 'create'])->name('partners.users.create');
    Route::post('/partners/users', [PartnerUserController::class, 'store'])->name('partners.users.store');
});

