<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test/{id}', function (string $id) {
    return json_encode(['Test' => $id]);
});
