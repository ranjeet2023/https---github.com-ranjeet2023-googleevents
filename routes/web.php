<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleCalendarController;
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


Route::get('/',[GoogleCalendarController::class,'redirectToGoogle']);
Route::get('/auth/google/upcoming',[GoogleCalendarController::class,'GoogleUpcoingEvents']);
Route::get('/google/redirect', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.redirect');

