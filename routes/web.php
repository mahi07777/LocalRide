<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('auth.login');
});



Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/search', [TaxiController::class, 'showSearchForm'])->name('taxi.search');
    Route::post('/fare', [TaxiController::class, 'calculateFare'])->name('fare.calculate'); // Public access
    Route::post('/book', [TaxiController::class, 'bookTaxi'])->name('taxi.book');
    Route::get('/bookings', [TaxiController::class, 'listBookings'])->name('taxi.bookings');
});

