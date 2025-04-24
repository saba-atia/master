<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
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



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');

Route::post('/request-demo', [ContactController::class, 'sendContact']);

require __DIR__.'/auth.php';


// Route::get('/dash', function () {
//     return view('dash.pages.dashboard');
// })->name('dash');
Route::get('/attendance', function () {
    return view('dash.pages.attendance');
})->name('attendance');

Route::get('/leaves', function () {
    return view('dash.pages.leaves');
})->name('leaves');

Route::get('/evaluations', function () {
    return view('dash.pages.evaluations');
})->name('evaluations');

Route::get('/reports', function () {
    return view('dash.pages.reports');
})->name('reports');

Route::get('/profile', function () {
    return view('dash.pages.profile');
})->name('profile');





Route::get('/finance', function () {
    return view('dash.pages.finance');
})->name('finance');

Route::get('/birthdays', function () {
    return view('dash.pages.birthdays');
})->name('birthdays');





// Route::get('/profile', function () {
//     return view('dash.pages.profile');
// })->name('profile.show');