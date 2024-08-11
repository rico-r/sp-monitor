<?php

use App\Http\Controllers\MobileAccountController;
use App\Http\Controllers\MobileAdminController;
use App\Http\Controllers\MobileLoginController;
use App\Http\Controllers\MobileMonitoringController;
use App\Http\Controllers\MobileRegisterController;
use App\Http\Controllers\MobileSuratPeringatanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//register
Route::get('checkconnection', [MobileRegisterController::class, 'checkConnection']);
Route::post('registermobile', [MobileRegisterController::class, 'register']);

//login
Route::post('loginmobile',[MobileLoginController::class, 'login']);

Route::middleware(['auth:api', 'auth.with.api.token:Direksi,Kepala Cabang,Supervisor,Admin Kas,Account Officer,Admin'])->group(function () {
    
    //suratperingatan
    Route::get('nasabah', [MobileSuratPeringatanController::class, 'getNasabahSP']);
    Route::post('surat_peringatan', [MobileSuratPeringatanController::class, 'SuratPeringatan']);
    Route::get('surat-peringatan/gambar/{filename}', [MobileSuratPeringatanController::class, 'serveImage']);
    Route::get('surat-peringatan/pdf/{filename}', [MobileSuratPeringatanController::class ,'servePdf']);

    //account
    Route::get('usermobile',[MobileAccountController::class, 'getUserDetails']);
    Route::post('logoutmobile', [MobileAccountController::class, 'logout']);


    //monitoring
    Route::get('nasabahs', [MobileMonitoringController::class, 'getNasabah']);

    //admin
    Route::get('alldata', [MobileAdminController::class, 'getAllData']);
    Route::put('user/update/{id}', [MobileAdminController::class, 'updateUser']);
    Route::get('usermobileadmin',[MobileAdminController::class, 'getUserAdmin']);


});