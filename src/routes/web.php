<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Illuminate\Http\Request;


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
Route::post('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/login', function () {
    return app(LoginViewResponse::class);
});



Route::middleware(['auth', 'verified'])->group(function () { 
    Route::get('/attendance/list', [UserController::class, 'index']);
     Route::get('/attendance', [UserController::class, 'create']); 
     Route::post('/attendance', [UserController::class, 'action']); 
     Route::get('/attendance/detail/{id}', [UserController::class, 'detail'])->name('attendance.detail');
     Route::post('/attendance/detail/{id}', [UserController::class, 'update']);
     Route::post('/logout', [UserController::class, 'logout']);
});

Route::middleware(['auth:admin'])->group(function () { 
    Route::get('/admin/attendance/list', [AdminController::class, 'index']);
    Route::get('/admin/staff/list', [AdminController::class, 'staff']);
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'attendance']);
    Route::get('/admin/attendance/staff/{id}/csv', [AdminController::class, 'exportCsv']);
    Route::get('/admin/attendance/{id}', [AdminController::class, 'detail']);
    Route::post('/admin/attendance/{id}', [AdminController::class, 'update']);
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminController::class, 'showApprove'])->name('stamp_correction_request.approve');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminController::class, 'approve']);
    Route::post('/admin/logout', [AdminController::class, 'logout']);
  });

  Route::get('/stamp_correction_request/list', [UserController::class, 'request'])
    ->middleware(['auth:web,admin']);