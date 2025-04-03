<?php
declare(strict_types=1);

use App\Http\Controllers\AJAX\ImageAJAXController;
use App\Http\Controllers\AJAX\LoginAJAXController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AJAX\UserAJAXController;
use App\Http\Controllers\AJAX\AccountAJAXController;
use App\Http\Controllers\AJAX\LocationAJAXController;
use App\Http\Controllers\AJAX\PlaceAJAXController;
use App\Http\Controllers\AJAX\ExhibitAJAXController;
use App\Http\Controllers\AJAX\RubricAJAXController;

Route::prefix('verwaltung/ajax')->name('ajax.')->group(static function () {
	Route::post('/user/login', [LoginAJAXController::class, 'login'])
		->name('user.login');
});

Route::prefix('verwaltung/ajax')->name('ajax.')->middleware('auth')->group(static function () {
	Route::post('/user/logout', [LoginAJAXController::class, 'logout'])
		->name('user.logout');
	
	# --- Benutzerverwaltung ---
	Route::get('/users', [UserAJAXController::class, 'query'])
		->name('user.query');

	Route::post('/user', [UserAJAXController::class, 'create'])
		->name('user.create');
	
	Route::put('/user/{user_id}', [UserAJAXController::class, 'update'])
		->name('user.update');
	
	Route::put('/user/{user_id}/set_password', [UserAJAXController::class, 'set_password'])
		->name('user.set_password');
	
	Route::delete('/user/{user_id}', [UserAJAXController::class, 'delete'])
		->name('user.delete');
	
	# --- Account / Konto ---
	Route::patch('/account/change_password', [AccountAJAXController::class, 'change_password'])
		->name('account.change_password');
	
	# --- Locations / Standorte ---
	Route::get('/locations', [LocationAJAXController::class, 'query'])
		->name('location.query');

	Route::post('/location', [LocationAJAXController::class, 'create'])
		->name('location.create');

	Route::put('/location/{location_id}', [LocationAJAXController::class, 'update'])
		->name('location.update');

	Route::delete('/location/{location_id}', [LocationAJAXController::class, 'delete'])
		->name('location.delete');

	# --- Plätze / Places ---
	Route::get('/places', [PlaceAJAXController::class, 'query'])
		->name('place.query');
	
	// TODO URI should by /place
	Route::post('location/{location_id}/place', [PlaceAJAXController::class, 'create'])
		->name('place.create');

	Route::put('/place/{place_id}', [PlaceAJAXController::class, 'update'])
		->name('place.update');

	Route::delete('/place/{place_id}', [PlaceAJAXController::class, 'delete'])
		->name('place.delete');

	# --- Exponate ---
	Route::get('/exhibits/tiles', [ExhibitAJAXController::class, 'tiles_query'])
		->name('exhibit.tiles.query');
	
	Route::get('/find/exhibit', [ExhibitAJAXController::class, 'connected_exhibits_query'])
		->name('exhibit.connected.query');
	
	Route::post('/exhibit', [ExhibitAJAXController::class, 'create'])
		->name('exhibit.create');
	
	Route::put('/exhibit/{exhibit_id}', [ExhibitAJAXController::class, 'update'])
		->name('exhibit.update');

	// siehe freetexts.d.ts
	Route::post('/exhibit/{exhibit_id}/free_text', [ExhibitAJAXController::class, 'create_free_text'])
		->name('exhibit.free_text.create');

	Route::put('/exhibit/{exhibit_id}/free_text/{free_text_id}', [ExhibitAJAXController::class, 'update_free_text'])
		->name('exhibit.free_text.update');

	Route::delete('/exhibit/{exhibit_id}/free_text/{free_text_id}', [ExhibitAJAXController::class, 'delete_free_text'])
		->name('exhibit.free_text.delete');

	Route::patch('/exhibit/{exhibit_id}/free_text/{free_text_id}', [ExhibitAJAXController::class, 'move_free_text'])
		->name('exhibit.free_text.move');
	
	// --- Exporte ---
	Route::get('/exhibit/{exhibit_id}/qr_code', [ExhibitAJAXController::class, 'get_qr_code'])
		->name('exhibit.get_qr_code');

	Route::get('/exhibit/{exhibit_id}/data_sheet', [ExhibitAJAXController::class, 'get_data_sheet'])
		->name('exhibit.get_data_sheet');
	
	Route::get('/exhibit/{exhibit_id}/qr_code/basic_script', [ExhibitAJAXController::class, 'get_qr_code_basic_script'])
		->name('exhibit.get_qr_code_basic_script');
	
	// --- Bilder ---
	// siehe images.d.ts
	Route::get('/image/{image_id}', [ImageAJAXController::class, 'get_image'])
		->name('image.get_image');

	Route::get('/thumbnail/{image_id}', [ImageAJAXController::class, 'get_thumbnail'])
		->name('image.get_thumbnail');
	
	Route::post('/exhibit/{exhibit_id}/image', [ImageAJAXController::class, 'create'])
		->name('exhibit.image.create');

	Route::patch('/image/{image_id}', [ImageAJAXController::class, 'update_meta_data'])
		->name('image.update_meta_data');

	// muss aus HTML-Spec-Gründen POST sein, da FormDate versendet wird, ist aber auch sonst gut so
	Route::post('/exhibit/{exhibit_id}/image/{image_id}', [ImageAJAXController::class, 'replace'])
		->name('exhibit.image.replace');

	Route::delete('/exhibit/{exhibit_id}/image/{image_id}', [ImageAJAXController::class, 'delete'])
		->name('exhibit.image.delete');
	
	Route::patch('/exhibit/{exhibit_id}/images', [ImageAJAXController::class, 'move'])
		->name('exhibit.image.move');

	// --- Rubriken ---
	Route::get('/rubrics', [RubricAJAXController::class, 'query'])
		->name('rubric.query');

	Route::post('/rubric', [RubricAJAXController::class, 'create'])
		->name('rubric.create');

	Route::put('/rubric/{rubric_id}', [RubricAJAXController::class, 'update'])
		->name('rubric.update');

	Route::delete('/rubric/{rubric_id}', [RubricAJAXController::class, 'delete'])
		->name('rubric.delete');
});
