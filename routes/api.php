<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\PayslipController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Attendance
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/today', [AttendanceController::class, 'today']);
        Route::get('/attendance/logs', [AttendanceController::class, 'logs']);

        // Leave
        Route::get('/leave/types', [LeaveController::class, 'types']);
        Route::get('/leave/balances', [LeaveController::class, 'balances']);
        Route::get('/leave/requests', [LeaveController::class, 'index']);
        Route::post('/leave/requests', [LeaveController::class, 'store']);

        // Payslip
        Route::get('/payslips', [PayslipController::class, 'index']);
        Route::get('/payslips/{id}', [PayslipController::class, 'show']);

        // Approval
        Route::get('/approvals/inbox', [ApprovalController::class, 'inbox']);
        Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve']);
        Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject']);
    });
});
