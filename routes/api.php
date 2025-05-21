<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;



Route::middleware('auth:sanctum')->group(function () {


});

Route::get('/attendance', [AttendanceController::class, 'getAttendanceApi']);
