<?php
declare(strict_types=1);

use App\Http\Controllers\AJAX\UserAJAXController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajax')->group(static function() {
	Route::patch('/user/{username}/set_admin', [ UserAJAXController::class, 'set_admin'])
		->name('ajax.user.set_admin');
});
