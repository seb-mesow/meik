<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(static function() {
	Route::get('/test/{id}', function (string $id) {
		return json_encode(['Test' => $id]);
	});
});
