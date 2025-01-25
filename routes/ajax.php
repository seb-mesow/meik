<?php
declare(strict_types=1);

use App\Http\Controllers\AJAX\ImageAJAXController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AJAX\UserAJAXController;
use App\Http\Controllers\AJAX\LocationAJAXController;
use App\Http\Controllers\AJAX\PlaceAJAXController;
use App\Http\Controllers\AJAX\ExhibitAJAXController;

Route::prefix('ajax')->group(static function() {
	# --- Benutzerverwaltung ---
	Route::patch('/user/{username}/set_admin', [UserAJAXController::class, 'set_admin'])
		->name('ajax.user.set_admin');

	# --- Locations / Standorte ---
	Route::get('/locations', [LocationAJAXController::class, 'get_paginated'])
		->name('ajax.location.get_paginated');
	
	Route::post('/location', [LocationAJAXController::class, 'create'])
		->name('ajax.location.create');
	
	Route::put('/location/{location_id}', [LocationAJAXController::class, 'update'])
		->name('ajax.location.update');
	
	Route::delete('/location/{location_id}', [LocationAJAXController::class, 'delete'])
		->name('ajax.location.delete');
	
	# --- Plätze / Places ---
	Route::get('/location/{location_id}/places', [PlaceAJAXController::class, 'get_paginated'])
		->name('ajax.place.get_paginated');
	
	Route::post('location/{location_id}/place', [PlaceAJAXController::class, 'create'])
		->name('ajax.place.create');
	
	Route::put('/place/{place_id}', [PlaceAJAXController::class, 'update'])
		->name('ajax.place.update');
	
	Route::delete('/place/{place_id}', [PlaceAJAXController::class, 'delete'])
		->name('ajax.place.delete');
	
	# --- Exponate ---
	Route::patch('/exhibit/{exhibit_id}/metadata', [ExhibitAJAXController::class, 'set_metadata'])
		->name('ajax.exhibit.set_metadata');
	
	// siehe freetexts.d.ts
	Route::post('/exhibit/{exhibit_id}/free_text', [ExhibitAJAXController::class, 'create_free_text'])
		->name('ajax.exhibit.free_text.create');
	
	Route::put('/exhibit/{exhibit_id}/free_text/{free_text_id}', [ExhibitAJAXController::class, 'update_free_text'])
		->name('ajax.exhibit.free_text.update');
	
	Route::delete('/exhibit/{exhibit_id}/free_text/{free_text_id}', [ExhibitAJAXController::class, 'delete_free_text'])
		->name('ajax.exhibit.free_text.delete');
	
	Route::patch('/exhibit/{exhibit_id}/free_text/{free_text_id}', [ExhibitAJAXController::class, 'move_free_text'])
		->name('ajax.exhibit.free_text.move');
	
	// --- Image ---
	// siehe images.d.ts
	Route::post('/exhibit/{exhibit_id}/image', [ImageAJAXController::class, 'create'])
		->name('ajax.exhibit.image.create');
	
	// muss aus HTML-Spec-Gründen POST sein, da FormDate versendet wird, sollte aber eigentlich PUT sein
	Route::post('/image/{image_id}', [ImageAJAXController::class, 'update'])
		->name('ajax.image.update');
	
	Route::delete('/exhibit/{exhibit_id}/image/{image_id}', [ImageAJAXController::class, 'delete'])
		->name('ajax.exhibit.image.delete');
	
	Route::patch('/exhibit/{exhibit_id}/image/{image_id}', [ImageAJAXController::class, 'move'])
		->name('ajax.exhibit.image.move');
	
	Route::get('/image/{image_id}', [ImageAJAXController::class, 'get_file'])
		->name('ajax.image.get_file');
	
	Route::get('/thumbnail/{image_id}', [ImageAJAXController::class, 'get_thumbnail_file'])
		->name('ajax.thumbnail.get_file');
	
	// --- Exporte ---
	Route::get('/exhibit/{exhibit_id}/qr', [ExhibitAJAXController::class, 'get_qr_code'])
		->name('ajax.exhibit.get_qr_code');
});
