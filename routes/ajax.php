<?php

declare(strict_types=1);

use App\Http\Controllers\AJAX\LocationAJAXController;
use App\Http\Controllers\AJAX\PlaceAJAXController;
use App\Http\Controllers\AJAX\UserAJAXController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')->group(static function () {
	Route::patch('/user/{username}/set_admin', [UserAJAXController::class, 'set_admin'])
		->name('ajax.user.set_admin');

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
});
