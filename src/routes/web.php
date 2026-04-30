<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

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
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['auth', 'verified'])->group(function () { 
    Route::get('/attendance/list', [UserController::class, 'index']); 
     Route::get('/attendance', [UserController::class, 'create']); 
     Route::post('/attendance', [UserController::class, 'action']); 
     Route::get('/attendance/detail/{attendance_id}', [UserController::class, 'detail']);
     Route::post('/attendance/detail/{attendance_id}', [UserController::class, 'update']);
     Route::get('/stamp_correction_request/list', [UserController::class, 'request']);
     

});

 Route::get('/admin/attendance/list', [AdminController::class, 'index']);