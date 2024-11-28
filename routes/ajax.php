<?php

declare(strict_types=1);

use App\Http\Controllers\AJAX\LocationAJAXController;
use App\Http\Controllers\AJAX\UserAJAXController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')->group(static function () {
	Route::patch('/user/{username}/set_admin', [UserAJAXController::class, 'set_admin'])
		->name('ajax.user.set_admin');

	Route::post('/locations', [LocationAJAXController::class, 'post_location'])
		->name('ajax.location.post_location');

	Route::put('/locations', [LocationAJAXController::class, 'put_location'])
		->name('ajax.location.put_location');


	Route::patch('/locations', [LocationAJAXController::class, 'patch_location'])
		->name('ajax.location.post_location');
});
