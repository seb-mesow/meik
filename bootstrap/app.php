<?php
declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: [
			__DIR__.'/../routes/web.php',
			__DIR__.'/../routes/ajax.php'
		],
		api: __DIR__.'/../routes/api.php',
		commands: __DIR__.'/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware) {
		$middleware->web(append: [
			\App\Http\Middleware\HandleInertiaRequests::class,
			\Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
		]);
		$middleware->remove([
			ConvertEmptyStringsToNull::class
		]);
	})
	->withExceptions(function (Exceptions $exceptions) {
		//
	})->create();
