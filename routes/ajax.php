<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AJAX\UserAJAXController;
use App\Http\Controllers\AJAX\LocationAJAXController;
use App\Http\Controllers\AJAX\PlaceAJAXController;
use App\Http\Controllers\AJAX\ExhibitAJAXController;

Route::prefix('ajax')->group(static function() {
	# --- Benutzerverwaltung ---
	Route::patch('/user/{username}/set_admin', [ UserAJAXController::class, 'set_admin'])
		->name('user.set_admin');
	Route::patch('/user/{username}/set_admin', [UserAJAXController::class, 'set_admin'])
		->name('ajax.user.set_admin');

	# --- Locations / Standorte ---
	Route::post('/locations', [LocationAJAXController::class, 'post_location'])
		->name('ajax.location.post_location');
	Route::put('/locations', [LocationAJAXController::class, 'put_location'])
		->name('ajax.location.put_location');
	Route::delete('/locations/{id}', [LocationAJAXController::class, 'delete_location'])
		->name('ajax.location.delete_location');
	Route::patch('/locations', [LocationAJAXController::class, 'patch_location'])
		->name('ajax.location.post_location');
	Route::get('locations', [LocationAJAXController::class, 'get_locations_paginated'])
		->name('ajax.location.get_locations_paginated');
	Route::post('/locations', [LocationAJAXController::class, 'post_location'])
		->name('ajax.location.post_location');
	
	# --- Plätze / Places ---
	Route::put('/places', [PlaceAJAXController::class, 'put_place'])
		->name('ajax.place.put_place');
	Route::delete('/places/{id}', [PlaceAJAXController::class, 'delete_place'])
		->name('ajax.place.delete_place');
	Route::patch('/places', [PlaceAJAXController::class, 'patch_place'])
		->name('ajax.place.post_place');
	Route::get('/places', [PlaceAJAXController::class, 'get_places_paginated'])
		->name('ajax.place.get_places_paginated');
	Route::post('/places', [PlaceAJAXController::class, 'post_place'])
		->name('ajax.location.post_place');
	
	# --- Exponate ---
	Route::patch('/exhibit/{id}/metadata', [ExhibitAJAXController::class, 'set_metadata'])
		->name('exhibit.set_metadata');
	Route::post('/exhibit/{id}/free_text/{free_text_index}', [ExhibitAJAXController::class, 'create_free_text'])
		->name('exhibit.free_text.create');
	Route::put('/exhibit/{id}/free_text/{free_text_index}', [ExhibitAJAXController::class, 'update_free_text'])
		->name('exhibit.free_text.update');
	Route::delete('/exhibit/{id}/free_text/{free_text_index}', [ExhibitAJAXController::class, 'delete_free_text'])
		->name('exhibit.free_text.delete');
});
