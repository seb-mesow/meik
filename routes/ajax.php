<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('ajax')->group(static function() {
	Route::get('/', function (string $id) {
		return json_encode(['Test' => $id]);
	});
});
