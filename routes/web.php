<?php

use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AttendanceController,
    BirthdayController,
    BranchController,
    ContactController,
    DashboardController,
    DepartmentController,
    EvaluationController,
    FinanceController,
    LeaveController,
    ProfileController,
    ReportController,
    VacationController
};
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Auth\ChangePasswordController;

// ========== Public Routes ==========
Route::get('/', fn () => view('home.home'))->name('home');
Route::get('/about', fn () => view('home.about'));
Route::get('/contact', fn () => view('home.contact'));
Route::get('/service', fn () => view('home.service'));
Route::get('/faq', fn () => view('home.faq'));

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/request-demo', [ContactController::class, 'sendContact']);

// ========== Authentication Routes ==========
require __DIR__ . '/../routes/auth.php';

// ========== Authenticated User Routes ==========
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/password/change', [ChangePasswordController::class, 'edit'])->name('profile.password.change');
        Route::put('/password/update', [ChangePasswordController::class, 'update'])->name('profile.password.update');
        Route::get('reset-password', [PasswordController::class, 'create']);

    });

    // Attendance
    Route::prefix('attendance')->group(function () {
       Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');

        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkOut');
        Route::post('/manual-entry', [AttendanceController::class, 'manualEntry'])->name('attendance.manualEntry');
        Route::get('/calendar-data', [AttendanceController::class, 'calendarData'])->name('attendance.calendarData');
        Route::get('/monthly-summary', [AttendanceController::class, 'monthlySummary'])->name('attendance.monthlySummary');
        Route::get('/filter', [AttendanceController::class, 'filter'])->name('attendance.filter');
        Route::get('/absentees', [AttendanceController::class, 'todaysAbsentees'])->name('attendance.absentees');
        Route::get('/check-leave-status', [AttendanceController::class, 'checkLeaveStatus'])->name('attendance.check-leave-status');
        Route::post('/set-hours', [AttendanceController::class, 'setRequiredHours'])->name('attendance.set-hours');
        Route::post('/', [AttendanceController::class, 'store'])->name('attendance.store');
    });

    // Leaves
    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/', [LeaveController::class, 'store'])->name('leaves.store');
        Route::put('/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
        Route::get('/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    });

    // Vacations
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
    Route::resource('evaluations', EvaluationController::class)
        ->middleware('can:viewAny,App\Models\Evaluation');

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
    Route::get('/notifications', fn () => view('dash.pages.notifications'))->name('notifications.index');

    // Department
    Route::get('/department/employees', [DepartmentController::class, 'employees'])->name('department.employees');
    Route::get('/department/leaves', [DepartmentController::class, 'leaves'])->name('department.leaves');
});

// ========== Admin Routes ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');

    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store'); // تم نقله هنا


    Route::get('employees/inactive', [EmployeeController::class, 'inactiveEmployees'])->name('employees.inactive');

    Route::post('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');

    Route::patch('employees/{employee}/deactivate', [EmployeeController::class, 'deactivate'])->name('employees.deactivate');

    Route::patch('/employees/{employee}/activate', [EmployeeController::class, 'activate'])
         ->name('employees.activate');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');

});




