<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/user-login', [AuthController::class, 'userLogin'])->name('userLogin');
Route::middleware(['auth'])->group(function(){
    Route::get('/user-logout', [AuthController::class, 'userLogout'])->name('userLogout');
    Route::resource('dashboard', DashboardController::class);
    Route::resource('custom-field', CustomFieldController::class);
    Route::get('get-contacts/{id}', [DashboardController::class, 'getContact'])->name('getContact');
    Route::post('merge-contact', [DashboardController::class, 'mergeContact'])->name('mergeContact');
});
