<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

# Das Prefix '/api' wird automatisch ergänzt.
Route::get('/test/{id}', function (string $id) {
	return json_encode(['Test' => $id]);
});
