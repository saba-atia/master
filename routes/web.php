<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordController;
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
        Route::put('/update-password', [PasswordController::class, 'update'])->name('password.update');
    });

    // Attendance Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/register', [AttendanceController::class, 'registerAttendance']);

    // Leaves Routes - الجديدة بدون تعارض
    Route::middleware(['auth'])->group(function () {
        // عرض الطلبات
        Route::get('/leaves', [LeaveController::class, 'index'])
             ->name('leaves.index');
    
        // تقديم طلب جديد
        Route::post('/leaves', [LeaveController::class, 'store'])
             ->name('leaves.store');
    
        // تحديث حالة الطلب (Approve / Reject)
        Route::put('/leaves/{id}/status', [LeaveController::class, 'updateStatus'])
             ->name('leaves.updateStatus');
    });
    Route::get('/leave-requests', [LeaveController::class, 'index'])->name('leave.index');

    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        // Branch Routes
        Route::prefix('branches')->group(function () {
            Route::get('/', [BranchController::class, 'index'])->name('branches.index');
            Route::get('/create', [BranchController::class, 'create'])->name('branches.create');
            Route::post('/', [BranchController::class, 'store'])->name('branches.store');
            Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
            Route::put('/{branch}', [BranchController::class, 'update'])->name('branches.update');
            Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
            
            // Map Routes
            Route::get('/map', [BranchController::class, 'showMap'])->name('branches.map');
            Route::post('/save-location', [BranchController::class, 'saveLocation'])->name('branches.save-location');
        });
    });

    // Dashboard Routes
    Route::prefix('dash')->group(function () {
        Route::get('/attendance', function () { return view('dash.pages.attendance'); })->name('attendance');
        Route::get('/evaluations', function () { return view('dash.pages.evaluations'); })->name('evaluations');
        Route::get('/reports', function () { return view('dash.pages.reports'); })->name('reports');
        Route::get('/finance', function () { return view('dash.pages.finance'); })->name('finance');
        Route::get('/birthdays', function () { return view('dash.pages.birthdays'); })->name('birthdays');
    });
});

Route::post('/validate-current-password', [PasswordController::class, 'validateCurrentPassword']);