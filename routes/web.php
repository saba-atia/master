<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VacationController;
use Illuminate\Support\Facades\Route;

// Public Routes
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/home', function () {
    return view('home.home');
})->name('home'); 

Route::get('/about', function () {
    return view('home.about');
});

Route::get('/contact', function () {
    return view('home.contact');
});

Route::get('/service', function () {
    return view('home.service');
});

Route::get('/faq', function () {
    return view('home.faq');
});

Route::get('/reports', [ReportController::class, 'index'])->name('reports');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/request-demo', [ContactController::class, 'sendContact']);

// Authentication Routes
require __DIR__.'/../routes/auth.php';

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/password/change', [ChangePasswordController::class, 'edit'])
            ->name('profile.password.change');
        Route::put('/password/update', [ChangePasswordController::class, 'update'])
            ->name('profile.password.update');
    });

    // Attendance Routes
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
        Route::post('/manual-entry', [AttendanceController::class, 'manualEntry'])->name('attendance.manualEntry');
        Route::get('/calendar-data', [AttendanceController::class, 'calendarData'])->name('attendance.calendarData');
        Route::get('/monthly-summary', [AttendanceController::class, 'monthlySummary'])->name('attendance.monthlySummary');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    });
    // Add to web.php
Route::get('attendance/filter', [AttendanceController::class, 'filter'])->name('attendance.filter'); 
Route::get('/attendance/absentees', [AttendanceController::class, 'todaysAbsentees'])->name('attendance.absentees');
  Route::get('/check-leave-status', [AttendanceController::class, 'checkLeaveStatus'])
        ->name('attendance.check-leave-status');
        Route::post('/attendance/set-hours', [AttendanceController::class, 'setRequiredHours'])
    ->name('attendance.set-hours');
    Route::post('/attendance', [App\Http\Controllers\AttendanceController::class, 'store'])
    ->name('attendance.store');

    // Leaves Routes
    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/', [LeaveController::class, 'store'])->name('leaves.store');
        Route::put('/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
        Route::get('/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    });

    // Vacations Routes
    Route::prefix('vacations')->group(function () {
        Route::get('/', [VacationController::class, 'index'])->name('vacations.index');
        Route::get('/create', [VacationController::class, 'create'])->name('vacations.create');
        Route::post('/', [VacationController::class, 'store'])->name('vacations.store');
        Route::get('/{vacation}/edit', [VacationController::class, 'edit'])->name('vacations.edit');
        Route::put('/{vacation}', [VacationController::class, 'update'])->name('vacations.update');
        Route::delete('/{vacation}', [VacationController::class, 'destroy'])->name('vacations.destroy');
        Route::get('/{vacation}', [VacationController::class, 'show'])->name('vacations.show');
    });

    // Evaluations
    Route::resource('evaluations', EvaluationController::class)->middleware('can:viewAny,App\Models\Evaluation');

    // Birthdays
Route::prefix('birthdays')->group(function () {
    Route::get('/', [BirthdayController::class, 'index'])->name('birthdays.index');
    Route::get('/wishes', [BirthdayController::class, 'showWishes'])->name('birthdays.wishes');
    Route::post('/wish/{user}', [BirthdayController::class, 'sendWish'])->name('birthdays.wish');
});

    // Finance
    Route::prefix('finance')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/transactions', [FinanceController::class, 'store'])->name('finance.transactions.store');
        Route::get('/transactions/{transaction}/edit', [FinanceController::class, 'edit'])->name('finance.transactions.edit');
        Route::put('/transactions/{transaction}', [FinanceController::class, 'update'])->name('finance.transactions.update');
        Route::delete('/transactions/{transaction}', [FinanceController::class, 'destroy'])->name('finance.transactions.destroy');
    });

    // Notifications
    Route::get('/notifications', function () {
        return view('dash.pages.notifications');
    })->name('notifications.index');

    // Department
    Route::get('/department/employees', [DepartmentController::class, 'employees'])
        ->name('department.employees');
            Route::get('/leaves', [DepartmentController::class, 'leaves'])->name('department.leaves');
        Route::get('/', [LeaveController::class, 'index'])->name('leaves.index');

});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', EmployeeController::class);
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    Route::post('/backup', [AdminController::class, 'createBackup'])->name('backup.create');
});
Route::get('/employees/inactive', [EmployeeController::class, 'inactiveEmployees'])
    ->name('admin.employees.inactive');

Route::post('/employees/{employee}/restore', [EmployeeController::class, 'restore'])
    ->name('admin.employees.restore');
// API Routes
Route::prefix('api')->group(function () {
    Route::get('/birthdays/upcoming', [BirthdayController::class, 'apiUpcomingBirthdays']);
    Route::get('/notifications/count', [NotificationController::class, 'unreadCount']);
});
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::prefix('admin/employees')->group(function () {
    // ... other employee routes
    
    // Inactive employees routes
    Route::get('/inactive', [EmployeeController::class, 'inactiveEmployees'])
        ->name('admin.employees.inactive');
        
    Route::post('/{id}/restore', [EmployeeController::class, 'restore'])
        ->name('admin.employees.restore');
        
    Route::delete('/{id}/force-delete', [EmployeeController::class, 'forceDelete'])
        ->name('admin.employees.force-delete');
});

// Routes for Employee Management
Route::prefix('admin/employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('admin.employees.index');
    Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
    Route::post('/', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/{employee}', [EmployeeController::class, 'update'])->name('admin.employees.update');
    
    // Status management routes
    Route::patch('/{employee}/deactivate', [EmployeeController::class, 'deactivate'])->name('admin.employees.deactivate');
    Route::patch('/{employee}/activate', [EmployeeController::class, 'activate'])->name('admin.employees.activate');
    
    // Inactive employees list
    Route::get('/inactive', [EmployeeController::class, 'inactiveEmployees'])->name('admin.employees.inactive');
    
    // Permanent deletion
    Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employees.destroy');
});