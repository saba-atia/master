<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
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

Route::get('/evaluations', function () {
    return view('dash.pages.evaluations');
})->name('evaluations.index');

Route::get('/reports', [ReportController::class, 'index'])->name('reports');


Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/request-demo', [ContactController::class, 'sendContact']);

// Authentication Routes
require __DIR__.'/../routes/auth.php';

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
        Route::get('/password/change', [ChangePasswordController::class, 'edit'])
            ->name('profile.password.change');
        Route::put('/password/update', [ChangePasswordController::class, 'update'])
            ->name('profile.password.update');
    });

    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/register', [AttendanceController::class, 'registerAttendance']);
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
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

    // Branches Routes
    Route::prefix('branches')->group(function () {
Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('/create', [BranchController::class, 'create'])->name('branches.create');
        Route::post('/', [BranchController::class, 'store'])->name('branches.store');
        Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
        Route::put('/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
    });
Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations');

    // Birthday Routes
    Route::prefix('birthdays')->group(function () {
        Route::get('/', [BirthdayController::class, 'index'])->name('birthdays.index');
        Route::post('/wish/{user}', [BirthdayController::class, 'sendWish'])->name('birthdays.wish');
        Route::get('/wishes', [BirthdayController::class, 'showWishes'])->name('birthdays.wishes');
        Route::get('/birthdays', [BirthdayController::class, 'index'])->name('birthdays');
    });

    // Dashboard Routes
    Route::prefix('dash')->group(function () {
        Route::get('/attendance', function () { return view('dash.pages.attendance'); })->name('dash.attendance');
        Route::get('/evaluations', function () { return view('dash.pages.evaluations'); })->name('dash.evaluations');
        Route::get('/reports', function () { return view('dash.pages.reports'); })->name('dash.reports');
        Route::get('/finance', function () { return view('dash.pages.finance'); })->name('dash.finance');
    });

    // Finance routes
    Route::prefix('finance')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/transactions', [FinanceController::class, 'store'])->name('finance.transactions.store');
        Route::get('/transactions/{transaction}/edit', [FinanceController::class, 'edit'])->name('finance.transactions.edit');
        Route::put('/transactions/{transaction}', [FinanceController::class, 'update'])->name('finance.transactions.update');
        Route::delete('/transactions/{transaction}', [FinanceController::class, 'destroy'])->name('finance.transactions.destroy');
    });

    // Notifications route
    Route::get('/notifications', function () {
        return view('dash.pages.notifications');
    })->name('notifications.index');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('employees', EmployeeController::class);
    // ...

    
    // Admin settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    
    Route::post('/backup', [AdminController::class, 'createBackup'])->name('backup.create');
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/birthdays/upcoming', [BirthdayController::class, 'apiUpcomingBirthdays']);
    Route::get('/notifications/count', [NotificationController::class, 'unreadCount']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('evaluations', EvaluationController::class)->middleware('can:viewAny,App\Models\Evaluation');
    Route::resource('evaluations', EvaluationController::class);

// Use:
Route::resource('evaluations', EvaluationController::class)->except(['edit', 'update']);
});
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');