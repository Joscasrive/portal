<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});
Auth::routes();



Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();
Route::get('/customers', [\App\Http\Controllers\HomeController::class, 'customers'])->name('customers');
Auth::routes();
Route::post('/customers', [\App\Http\Controllers\HomeController::class, 'customers'])->name('customer');
Auth::routes();
Route::get('/conversations', [\App\Http\Controllers\HomeController::class, 'conversations'])->name('conversations');
Auth::routes();
Route::post('/requests', [\App\Http\Controllers\HomeController::class, 'requests'])->name('requests');
Auth::routes();
Route::post('/notes', [\App\Http\Controllers\HomeController::class, 'notes'])->name('notes');
Auth::routes();

