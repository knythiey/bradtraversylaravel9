<?php

use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::controller(ListingController::class)->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/listings/create', 'create');
        Route::get('/listings/manage', 'manage');
        Route::post('/listings', 'store');
        Route::get('/listings/{listing}/edit', 'edit');
        Route::put('/listings/{listing}', 'update');
        Route::delete('/listings/{listing}', 'destroy');
    });
    Route::get('/', 'index');
    Route::get('/listings/{listing}', 'show');

});

Route::controller(UserController::class)->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('/register', 'create');
        Route::get('/login', 'login')->name('login');
        Route::post('/users', 'store');
        Route::post('/users/authenticate', 'authenticate');
    });
    Route::post('/logout', 'logout')->middleware('auth');
});
