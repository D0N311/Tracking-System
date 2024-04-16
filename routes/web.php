<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerifyEmailController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}', [VerifyEmailController::class, 'verify'])->name('verification.verify');
// Route::get('/email/verify/{id}', [VerifyEmailController::class, 'verify'])
    // ->name('verification.verify');
