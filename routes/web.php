<?php

use App\Http\Controllers\ExhibitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard'); 
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	Route::get('/users', [UserController::class, 'all_users'])->name('users.all');
});

Route::get('/exhibits', [ExhibitController::class, 'get_all_exhibits']);
Route::get('/exhibit/{id}', [ExhibitController::class, 'get_exhibit']);
Route::post('/exhibit', [ExhibitController::class, 'post_exhibit']);
Route::put('/exhibit/{id}', [ExhibitController::class, 'put_exhibit']);
Route::delete('/exhibit/{id}', [ExhibitController::class, 'delete_exhibit']);

require __DIR__.'/auth.php';
