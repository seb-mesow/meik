<?php
declare(strict_types=1);

use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ExhibitController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\PlaceController;
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
	Route::get('/users', [UserController::class, 'overview'])->name('users.all');
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	Route::get('/exhibits', [ExhibitController::class, 'overview'])->name('exhibit.overview');
	Route::get('/exhibit', [ExhibitController::class, 'new'])->name('exhibit.new');
	Route::post('/exhibit', [ExhibitController::class, 'create'])->name('exhibit.create');
	Route::get('/exhibit/{id}', [ExhibitController::class, 'details'])->name('exhibit.details');
	Route::delete('/exhibit/{id}', [ExhibitController::class, 'delete'])->name('exhibit.delete');
	
	Route::get('/locations', [LocationController::class, 'overview'])->name('locations.all');
	Route::get('/places', [PlaceController::class, 'overview'])->name('places.all');
});

require __DIR__.'/auth.php';
