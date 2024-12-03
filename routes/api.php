<?php

use App\Http\Controllers\API\ExhibitAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

# Das Prefix '/api' wird automatisch ergänzt.
Route::get('/exhibits', [ExhibitAPIController::class, 'get_all_exhibits']);
