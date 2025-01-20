<?php
declare(strict_types=1);

use App\Http\Controllers\Web\ImagesController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ExhibitController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\PlaceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('exhibit.overview'); // redirekt mit 302
})->name('root');

Route::get('/dashboard', function () {
	return Inertia::render('Dashboard'); 
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
	# --- Users ---
	Route::get('/users', [UserController::class, 'overview'])->name('user.overview');
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	# --- Exhibits ---
	Route::get('/exhibits', [ExhibitController::class, 'overview'])->name('exhibit.overview');
	Route::get('/exhibit', [ExhibitController::class, 'new'])->name('exhibit.new');
	Route::post('/exhibit', [ExhibitController::class, 'create'])->name('exhibit.create');
	Route::get('/exhibit/{id}', [ExhibitController::class, 'details'])->name('exhibit.details');
	Route::delete('/exhibit/{id}', [ExhibitController::class, 'delete'])->name('exhibit.delete');
	
	# --- Images ---
	Route::get('/exhibit/{exhibit_id}/images', [ImagesController::class, 'details'])->name('exhibit.images.details');
	
	# --- Locations ---
	Route::get('/locations', [LocationController::class, 'overview'])->name('location.overview');
	
	# --- Places ---
	Route::get('/location/{location_id}/places', [PlaceController::class, 'overview'])->name('place.overview');
});

require __DIR__.'/auth.php';
