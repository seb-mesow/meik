<?php

declare(strict_types=1);

use App\Http\Controllers\AJAX\ImageAJAXController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AJAX\UserAJAXController;
use App\Http\Controllers\AJAX\LocationAJAXController;
use App\Http\Controllers\AJAX\PlaceAJAXController;
use App\Http\Controllers\AJAX\ExhibitAJAXController;
use App\Http\Controllers\AJAX\RubricAJAXController;

Route::prefix('ajax')->group(static function () {
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
	Route::get('/exhibit', [ExhibitAJAXController::class, 'get_paginated'])
		->name('ajax.exhibit.get_paginated');
	
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
	
	// --- Exporte ---
	Route::get('/exhibit/{exhibit_id}/qr', [ExhibitAJAXController::class, 'get_qr_code'])
		->name('ajax.exhibit.get_qr_code');

	Route::get('/exhibit/{exhibit_id}/data-sheet', [ExhibitAJAXController::class, 'get_data_sheet'])
		->name('ajax.exhibit.get_data_sheet');
	
	// --- Bilder ---
	// siehe images.d.ts
	Route::get('/image/{image_id}', [ImageAJAXController::class, 'get_image'])
		->name('ajax.image.get_image');

	Route::get('/thumbnail/{image_id}', [ImageAJAXController::class, 'get_thumbnail'])
		->name('ajax.image.get_thumbnail');
	
	Route::post('/exhibit/{exhibit_id}/image', [ImageAJAXController::class, 'create'])
		->name('ajax.exhibit.image.create');

	Route::patch('/image/{image_id}', [ImageAJAXController::class, 'update_meta_data'])
		->name('ajax.image.update_meta_data');

	// muss aus HTML-Spec-Gründen POST sein, da FormDate versendet wird, ist aber auch sonst gut so
	Route::post('/exhibit/{exhibit_id}/image/{image_id}', [ImageAJAXController::class, 'replace'])
		->name('ajax.exhibit.image.replace');

	Route::delete('/exhibit/{exhibit_id}/image/{image_id}', [ImageAJAXController::class, 'delete'])
		->name('ajax.exhibit.image.delete');

	Route::patch('/exhibit/{exhibit_id}/image/{image_id}', [ImageAJAXController::class, 'move'])
		->name('ajax.exhibit.image.move');

	// --- Rubriken ---
	Route::get('/rubric', [RubricAJAXController::class, 'get_paginated'])
		->name('ajax.rubric.get_paginated');

	Route::post('/rubric', [RubricAJAXController::class, 'create'])
		->name('ajax.rubric.create');

	Route::put('/rubric/{rubric_id}', [RubricAJAXController::class, 'update'])
		->name('ajax.rubric.update');

	Route::delete('/rubric/{rubric_id}', [RubricAJAXController::class, 'delete'])
		->name('ajax.rubric.delete');
});
