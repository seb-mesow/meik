<?php
declare(strict_types=1);

use App\Http\Controllers\Web\ImagesController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ExhibitController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\PlaceController;
use App\Http\Controllers\Web\RubricController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
	if (Auth::check()) {
		// !!! Ändere auch in LoginController::login() !!!
		return redirect()->route('category.overview');
	} else {
		return redirect()->route('login.form');
	}
})->name('root');

Route::get('/dashboard', function () {
	return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
	# --- Users ---
	Route::get('/users', [UserController::class, 'overview'])
		->name('user.overview');
	
	Route::get('/user/new', [UserController::class, 'new'])
		->name('user.new');
	
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	
	# --- Categories ---
	Route::get('/categories', [CategoryController::class, 'overview'])->name('category.overview');
	
	Route::get('/category/{category_id}', [CategoryController::class, 'details'])->name('category.details');
	
	# --- Rubric ---
	Route::get('/rubric/{rubric_id}', [RubricController::class, 'details'])->name('rubric.details');
	
	# --- Exhibits ---
	Route::get('/exhibits', [ExhibitController::class, 'overview'])->name('exhibit.overview');
	
	Route::get('/exhibit/new', [ExhibitController::class, 'new'])
		->name('exhibit.new');
	
	Route::get('/exhibit/{exhibit_id}', [ExhibitController::class, 'details'])
		->name('exhibit.details');
	
	# --- Images ---
	Route::get('/exhibit/{exhibit_id}/images', [ImagesController::class, 'details'])
		->name('exhibit.images.details');

	# --- Locations ---
	Route::get('/locations', [LocationController::class, 'overview'])
		->name('location.overview');

	# --- Places ---
	Route::get('/locations/{location_id}/places', [PlaceController::class, 'overview'])
		->name('place.overview');
});

require __DIR__ . '/auth.php';
