<?php

use App\Http\Controllers\API\ExhibitAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

# Das Prefix '/api' wird automatisch ergänzt.
Route::get('/exhibits', [ExhibitAPIController::class, 'get_exhibits_paginated']);

# Die Route kann dynamisch die Felder des Dokuments durchsuchen. 
# Die Params 'operator' und 'field[]' dienen zur Steuerung. Operator kann 'and' oder 'or' sein. 
# field[] kann mehrfach exisiteren und muss folgendem Muster folgen 'feldname':'wert'
Route::get('/exhibits/filter', [ExhibitAPIController::class, 'find_exhibits_by_filter']);

Route::get('/search/{query}', [ExhibitAPIController::class, 'search_exhibits']);
Route::get('/exhibit/{id}', [ExhibitAPIController::class, 'get_exhibit_by_id']);