<?php
declare(strict_types=1);

use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ExhibitController;
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
	Route::get('/users', [UserController::class, 'all_users'])->name('users.all');
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	Route::get('/exhibits', [ExhibitController::class, 'all_exhibits'])->name('exhibit.index');
	Route::post('/exhibit', [ExhibitController::class, 'post_exhibit'])->name('exhibit.store');
	Route::put('/exhibit/{id}', [ExhibitController::class, 'put_exhibit'])->name('exhibit.update');
	Route::get('/exhibit/{id}', [ExhibitController::class, 'get_exhibit'])->name('exhibit.show');
	// Route::delete('/exhibit/{id}', [ExhibitController::class, 'delete_exhibit']);
});

require __DIR__.'/auth.php';
