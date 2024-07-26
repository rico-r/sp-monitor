<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ApplicationController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('home',[HomeController::class,'index'])->name('home');
Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::get('nasabah/edit/{id}', [HomeController::class, 'editNasabah'])->name('nasabah.edit');
Route::post('nasabah/update/{id}', [HomeController::class, 'updateNasabah'])->name('nasabah.update');
Route::post('/nasabah', [HomeController::class, 'addNasabah'])->name('nasabah.store');
Route::delete('nasabah/delete/{id}', [HomeController::class, 'deleteNasabah'])->name('nasabah.delete');
Route::get('nasabah/detail/{id}', [HomeController::class, 'detailNasabah'])->name('nasabah.detail');
Route::post('nasabah/add', [HomeController::class, 'addNasabah'])->name('nasabah.add');

Route::get('/', [AuthController::class, 'index'])->name('login');

Route::post('login', [AuthController::class, 'postLogin'])->name('login.post');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister'])->name('register.post');
Route::get('user/verify/{token}', [AuthController::class, 'verifyEmail'])->name('user.verify');
Route::get('page/error', [AuthController::class, 'showErrorPage'])->name('page.error');


Route::get('forget-password', [AuthController::class, 'forgetPasswordForm'])->name('forget.password');
Route::post('forget-password', [AuthController::class, 'forgetPasswordPost'])->name('forget.password.post');
Route::get('reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [AuthController::class, 'resetPasswordPost'])->name('reset.password.post');

Route::middleware('auth:web')->group(function(){
    
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
        Route::get('change-password', [AuthController::class, 'changePasswordForm'])->name('password.change.form');
        Route::post('change-password', [AuthController::class, 'changePasswordPost'])->name('password.change.post');
        
});






