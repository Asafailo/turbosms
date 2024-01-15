<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

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

//Route::view('/', 'auth.login')->name('auth.login');
//Route::get('/test', [LoginController::class, 'testTurboSms'])
//    ->name('test.turbosms');

Route::view('/', 'auth.login')->name('auth.login');
Route::view('/register', 'auth.register')->name('auth.register');
Route::post('/sendRegister', [RegistrationController::class, 'registrationRequest'])->name('send.register');
Route::post('/registerUser', [RegistrationController::class, 'registerUser'])->name('register.user');
Route::post('/sendLogin', [LoginController::class, 'loginRequest'])->name('send.loginRequest');
Route::post('/loginUser', [LoginController::class, 'loginUser'])->name('login.user');
Route::view('/confirmation', 'auth.confirmation')->name('auth.confirmation');


Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard.welcome')->name('dashboard.welcome');
});

//Route::middleware('guest')->group(function(){
//
//
//});

//Route::get('/', function () {
//    return view('welcome');
//});
