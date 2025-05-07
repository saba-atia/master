<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VacationController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home.home');
});

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


Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/request-demo', [ContactController::class, 'sendContact']);

// Authentication Routes
require __DIR__.'/auth.php';

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Profile Routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password.change');
        Route::put('/password/update', [PasswordController::class, 'updatePassword'])->name('password.update');    });
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/register', [AttendanceController::class, 'registerAttendance']);

    // Leaves Routes - الجديدة بدون تعارض
    Route::middleware(['auth'])->group(function () {
        // مسارات إدارة الإجازات
        Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
        Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
        Route::put('/leaves/{leave}', [LeaveController::class, 'update'])->name('leaves.update');
        Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    });
    // Admin Routes
// إدارة الفروع
Route::prefix('branches')->group(function () {
    Route::get('/', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/', [BranchController::class, 'store'])->name('branches.store');
    Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    Route::put('/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
});

// تسجيل الحضور
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Dashboard Routes
    Route::prefix('dash')->group(function () {
        Route::get('/attendance', function () { return view('dash.pages.attendance'); })->name('attendance');
        Route::get('/evaluations', function () { return view('dash.pages.evaluations'); })->name('evaluations');
        Route::get('/reports', function () { return view('dash.pages.reports'); })->name('reports');
        Route::get('/finance', function () { return view('dash.pages.finance'); })->name('finance');
        Route::get('/birthdays', function () { return view('birthdays.index'); })->name('birthdays');
    });
});
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // جميع روابط الإدارة هنا
    Route::resource('employees', EmployeeController::class)->except(['show']);
    
    // يمكنك إضافة روابط أخرى خاصة بالإدارة
});

    
Route::post('/validate-current-password', [PasswordController::class, 'validateCurrentPassword']);

Route::middleware(['auth'])->group(function () {
    // Finance routes
    Route::prefix('finance')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/transactions', [FinanceController::class, 'store'])->name('finance.transactions.store');
        Route::get('/transactions/{transaction}/edit', [FinanceController::class, 'edit'])->name('finance.transactions.edit');
        Route::put('/transactions/{transaction}', [FinanceController::class, 'update'])->name('finance.transactions.update');
        Route::delete('/transactions/{transaction}', [FinanceController::class, 'destroy'])->name('finance.transactions.destroy');
    });
});

Route::resource('vacations', VacationController::class)->middleware('auth');


// Profile Routes
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    
    // Password Routes
    Route::get('/password', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/password', [PasswordController::class, 'updatePassword'])->name('password.update');
    
    // Birthday Route
    Route::get('/birthdays', [ProfileController::class, 'birthdays'])->name('birthdays.my');
});